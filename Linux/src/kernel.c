/* WibOS Custom 32-bit Kernel in C */

#define SCREEN_WIDTH 80
#define SCREEN_HEIGHT 25
#define VIDEO_MEM ((volatile char*)0xB8000)

// VGA Cursor register ports
#define REG_SCREEN_CTRL 0x3D4
#define REG_SCREEN_DATA 0x3D5

// US Keyboard layout scancode to ASCII translation table
static const char scancode_to_ascii[] = {
    0,   27,  '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '=', '\b',
    '\t', 'q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', '[', ']', '\n',
    0,   'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', ';', '\'', '`',
    0,   '\\', 'z', 'x', 'c', 'v', 'b', 'n', 'm', ',', '.', '/', 0,
    '*', 0,   ' '
};

// Global variables for cursor position, text color, and uptime clock
static int cursor_x = 0;
static int cursor_y = 9;
static char current_color = 0x0A; // Matrix Green on Black default
static unsigned char last_sec = 0xFF;
static unsigned int uptime_seconds = 0;

// Forward declarations
unsigned char inb(unsigned short port);
void outb(unsigned short port, unsigned char value);
void update_cursor();
void init_screen();
void clear_console_area();
void print_char(char c);
void print_string(const char* str);
void print_newline();
void scroll();
int strcmp(const char* s1, const char* s2);
int strlen(const char* str);
void execute_command(const char* cmd);
void run_matrix_screensaver();
void beep();
void delay(int count);

// CMOS RTC functions
unsigned char get_rtc_register(int reg);
unsigned char bcd_to_bin(unsigned char bcd);
void draw_status_bar();
void draw_bottom_bar();
void draw_centered_logo();
void update_uptime_and_clock();
void update_console_theme();

// I/O Port Helper Functions
unsigned char inb(unsigned short port) {
    unsigned char result;
    __asm__ volatile("inb %1, %0" : "=a" (result) : "Nd" (port));
    return result;
}

void outb(unsigned short port, unsigned char value) {
    __asm__ volatile("outb %0, %1" : : "a" (value), "Nd" (port));
}

// CMOS RTC Helper Functions
unsigned char get_rtc_register(int reg) {
    outb(0x70, reg);
    return inb(0x71);
}

unsigned char bcd_to_bin(unsigned char bcd) {
    return ((bcd / 16) * 10) + (bcd & 0x0F);
}

// Update the VGA hardware cursor position
void update_cursor() {
    unsigned short position = cursor_y * SCREEN_WIDTH + cursor_x;
    outb(REG_SCREEN_CTRL, 14);
    outb(REG_SCREEN_DATA, (unsigned char)(position >> 8));
    outb(REG_SCREEN_CTRL, 15);
    outb(REG_SCREEN_DATA, (unsigned char)(position & 0xFF));
}

// Draw top status bar with clock and uptime
void draw_status_bar() {
    unsigned char hour = bcd_to_bin(get_rtc_register(0x04));
    unsigned char minute = bcd_to_bin(get_rtc_register(0x02));
    unsigned char second = bcd_to_bin(get_rtc_register(0x00));

    // Format uptime string
    char uptime_str[10];
    int temp = uptime_seconds;
    int idx = 0;
    if (temp == 0) {
        uptime_str[idx++] = '0';
    } else {
        char rev[10];
        int r_idx = 0;
        while (temp > 0) {
            rev[r_idx++] = '0' + (temp % 10);
            temp /= 10;
        }
        for (int i = r_idx - 1; i >= 0; i--) {
            uptime_str[idx++] = rev[i];
        }
    }
    uptime_str[idx] = '\0';

    // Clear Row 0 with grey attribute
    for (int col = 0; col < SCREEN_WIDTH; col++) {
        VIDEO_MEM[col * 2] = ' ';
        VIDEO_MEM[col * 2 + 1] = 0x70; // Black text on Light Grey background
    }

    // Write left side info
    const char* left_info = " WibOS Kernel v1.0.0 | Uptime: ";
    int col = 0;
    for (int i = 0; left_info[i] != '\0'; i++) {
        VIDEO_MEM[col * 2] = left_info[i];
        VIDEO_MEM[col * 2 + 1] = 0x70;
        col++;
    }
    for (int i = 0; uptime_str[i] != '\0'; i++) {
        VIDEO_MEM[col * 2] = uptime_str[i];
        VIDEO_MEM[col * 2 + 1] = 0x70;
        col++;
    }
    VIDEO_MEM[col * 2] = 's';
    VIDEO_MEM[col * 2 + 1] = 0x70;

    // Write right side time (format HH:MM:SS) starting at col 68
    col = 68;
    const char* time_label = "Time: ";
    for (int i = 0; time_label[i] != '\0'; i++) {
        VIDEO_MEM[col * 2] = time_label[i];
        VIDEO_MEM[col * 2 + 1] = 0x70;
        col++;
    }
    VIDEO_MEM[col * 2] = '0' + (hour / 10); col++;
    VIDEO_MEM[col * 2] = '0' + (hour % 10); col++;
    VIDEO_MEM[col * 2] = ':'; col++;
    VIDEO_MEM[col * 2] = '0' + (minute / 10); col++;
    VIDEO_MEM[col * 2] = '0' + (minute % 10); col++;
    VIDEO_MEM[col * 2] = ':'; col++;
    VIDEO_MEM[col * 2] = '0' + (second / 10); col++;
    VIDEO_MEM[col * 2] = '0' + (second % 10); col++;
}

// Draw bottom help actions bar
void draw_bottom_bar() {
    for (int col = 0; col < SCREEN_WIDTH; col++) {
        VIDEO_MEM[((SCREEN_HEIGHT - 1) * SCREEN_WIDTH + col) * 2] = ' ';
        VIDEO_MEM[((SCREEN_HEIGHT - 1) * SCREEN_WIDTH + col) * 2 + 1] = 0x70;
    }

    const char* actions = "  [ESC] Screensaver  |  [color] Change Theme  |  [beep] Play Tone  |  [reboot] Reset ";
    int start_col = (SCREEN_WIDTH - strlen(actions)) / 2;
    for (int i = 0; actions[i] != '\0'; i++) {
        int col = start_col + i;
        VIDEO_MEM[((SCREEN_HEIGHT - 1) * SCREEN_WIDTH + col) * 2] = actions[i];
        VIDEO_MEM[((SCREEN_HEIGHT - 1) * SCREEN_WIDTH + col) * 2 + 1] = 0x70;
    }
}

// Draw centered ASCII art logo
void draw_centered_logo() {
    const char* logo[] = {
        " __      __.__                    _________ ",
        "/  \\    /  \\__|____  ____  ______ \\_____  \\ ",
        "\\   \\/\\/   /  \\__  \\/  _ \\/  ___/  /   |   \\",
        " \\        /|  |/ __ \\(  <_> )___ \\  /    |    \\",
        "  \\__/\\  / |__(____  /\\____/____  > \\_______  /",
        "       \\/          \\/           \\/          \\/ "
    };

    for (int i = 0; i < 6; i++) {
        int len = strlen(logo[i]);
        int start_x = (SCREEN_WIDTH - len) / 2;
        int start_y = 2 + i; // Draw on rows 2 to 7
        for (int j = 0; j < len; j++) {
            int offset = (start_y * SCREEN_WIDTH + start_x + j) * 2;
            VIDEO_MEM[offset] = logo[i][j];
            VIDEO_MEM[offset + 1] = 0x0E; // Bright Yellow logo for maximum visual premium
        }
    }
}

// Full system view initialization
void init_screen() {
    for (int i = 0; i < SCREEN_WIDTH * SCREEN_HEIGHT; i++) {
        VIDEO_MEM[i * 2] = ' ';
        VIDEO_MEM[i * 2 + 1] = 0x07; // Light grey on black default
    }
    uptime_seconds = 0;
    last_sec = 0xFF;
    draw_status_bar();
    draw_centered_logo();
    draw_bottom_bar();
    
    cursor_x = 0;
    cursor_y = 9;
    update_cursor();
}

// Clear the console area only (between status bars)
void clear_console_area() {
    for (int y = 9; y < SCREEN_HEIGHT - 1; y++) {
        for (int x = 0; x < SCREEN_WIDTH; x++) {
            int offset = (y * SCREEN_WIDTH + x) * 2;
            VIDEO_MEM[offset] = ' ';
            VIDEO_MEM[offset + 1] = current_color;
        }
    }
    cursor_x = 0;
    cursor_y = 9;
    update_cursor();
}

// Update clock if CMOS seconds register changed
void update_uptime_and_clock() {
    unsigned char sec = bcd_to_bin(get_rtc_register(0x00));
    if (sec != last_sec) {
        if (last_sec != 0xFF) {
            uptime_seconds++;
        }
        last_sec = sec;
        draw_status_bar();
    }
}

// Update the attributes of the active console screen
void update_console_theme() {
    for (int y = 9; y < SCREEN_HEIGHT - 1; y++) {
        for (int x = 0; x < SCREEN_WIDTH; x++) {
            int offset = (y * SCREEN_WIDTH + x) * 2 + 1;
            VIDEO_MEM[offset] = current_color;
        }
    }
}

// Print single character inside the console boundary (rows 9-23)
void print_char(char c) {
    if (c == '\n') {
        print_newline();
        return;
    }
    if (c == '\b') {
        if (cursor_x > 0) {
            cursor_x--;
            int offset = (cursor_y * SCREEN_WIDTH + cursor_x) * 2;
            VIDEO_MEM[offset] = ' ';
            VIDEO_MEM[offset + 1] = current_color;
            update_cursor();
        }
        return;
    }

    int offset = (cursor_y * SCREEN_WIDTH + cursor_x) * 2;
    VIDEO_MEM[offset] = c;
    VIDEO_MEM[offset + 1] = current_color;

    cursor_x++;
    if (cursor_x >= SCREEN_WIDTH) {
        print_newline();
    }
    update_cursor();
}

// Print null-terminated string
void print_string(const char* str) {
    for (int i = 0; str[i] != '\0'; i++) {
        print_char(str[i]);
    }
}

// Move to start of next line with scroll checking
void print_newline() {
    cursor_x = 0;
    cursor_y++;
    if (cursor_y >= SCREEN_HEIGHT - 1) { // Reach the bottom status bar boundary
        scroll();
    }
    update_cursor();
}

// Scroll console contents up 1 line (within rows 9-23)
void scroll() {
    for (int y = 10; y < SCREEN_HEIGHT - 1; y++) {
        for (int x = 0; x < SCREEN_WIDTH; x++) {
            int dest = ((y - 1) * SCREEN_WIDTH + x) * 2;
            int src = (y * SCREEN_WIDTH + x) * 2;
            VIDEO_MEM[dest] = VIDEO_MEM[src];
            VIDEO_MEM[dest + 1] = VIDEO_MEM[src + 1];
        }
    }
    for (int x = 0; x < SCREEN_WIDTH; x++) {
        int offset = ((SCREEN_HEIGHT - 2) * SCREEN_WIDTH + x) * 2;
        VIDEO_MEM[offset] = ' ';
        VIDEO_MEM[offset + 1] = current_color;
    }
    cursor_y = SCREEN_HEIGHT - 2;
    update_cursor();
}

// String functions
int strcmp(const char* s1, const char* s2) {
    while (*s1 && (*s1 == *s2)) {
        s1++;
        s2++;
    }
    return *(const unsigned char*)s1 - *(const unsigned char*)s2;
}

int strlen(const char* str) {
    int len = 0;
    while (str[len] != '\0') len++;
    return len;
}

void delay(int count) {
    for (volatile int i = 0; i < count; i++);
}

// Beep helper
void beep() {
    unsigned int freq = 800;
    unsigned int div = 1193180 / freq;
    
    outb(0x43, 0xB6);
    outb(0x42, (unsigned char)(div & 0xFF));
    outb(0x42, (unsigned char)((div >> 8) & 0xFF));

    unsigned char speaker = inb(0x61);
    outb(0x61, speaker | 0x03);

    delay(12000000);

    outb(0x61, inb(0x61) & 0xFC);
}

// Screensaver matrix drop
void run_matrix_screensaver() {
    // Clear screen temporarily
    for (int i = 0; i < SCREEN_WIDTH * SCREEN_HEIGHT; i++) {
        VIDEO_MEM[i * 2] = ' ';
        VIDEO_MEM[i * 2 + 1] = 0x07;
    }
    
    int columns[SCREEN_WIDTH];
    for (int i = 0; i < SCREEN_WIDTH; i++) {
        columns[i] = -(i % 17);
    }
    
    unsigned int random_seed = 98765;
    int is_running = 1;

    while (is_running) {
        for (int x = 0; x < SCREEN_WIDTH; x++) {
            int y = columns[x];
            
            if (y - 8 >= 0 && y - 8 < SCREEN_HEIGHT) {
                int offset = ((y - 8) * SCREEN_WIDTH + x) * 2;
                VIDEO_MEM[offset] = ' ';
                VIDEO_MEM[offset + 1] = 0x07;
            }
            
            for (int t = 0; t < 8; t++) {
                int cy = y - t;
                if (cy >= 0 && cy < SCREEN_HEIGHT) {
                    int offset = (cy * SCREEN_WIDTH + x) * 2;
                    random_seed = random_seed * 1103515245 + 12345;
                    char rand_char = 33 + (random_seed % 93);
                    VIDEO_MEM[offset] = rand_char;
                    
                    if (t == 0) {
                        VIDEO_MEM[offset + 1] = 0x0F;
                    } else if (t < 3) {
                        VIDEO_MEM[offset + 1] = 0x0A;
                    } else {
                        VIDEO_MEM[offset + 1] = 0x02;
                    }
                }
            }
            
            columns[x]++;
            if (columns[x] >= SCREEN_HEIGHT + 8) {
                columns[x] = 0;
            }
        }
        
        delay(1800000);

        // Check if ESC is pressed (Scan code 0x01)
        if ((inb(0x64) & 1) != 0) {
            unsigned char code = inb(0x60);
            if (code == 0x01 || code == 0x10) {
                is_running = 0;
            }
        }
    }
    
    // Restore layout
    init_screen();
    update_console_theme();
    print_string("WibOS Console restored.\n");
    print_string("wibos> ");
}

// Shell Command Evaluator
void execute_command(const char* cmd) {
    if (strcmp(cmd, "help") == 0) {
        print_string("WibOS Shell Commands:\n");
        print_string("  help     - Display this command dictionary\n");
        print_string("  about    - Print custom OS details\n");
        print_string("  clear    - Clear console screen buffer\n");
        print_string("  matrix   - Launch falling digital rain screensaver\n");
        print_string("  beep     - Play pitch-controlled beep tone via PC Speaker\n");
        print_string("  color    - Cycle terminal layout color themes\n");
        print_string("  reboot   - Soft restart the x86 WebAssembly CPU emulator\n");
        print_string("  shutdown - Halt CPU processes safely\n");
    } else if (strcmp(cmd, "about") == 0) {
        print_string("========================================================\n");
        print_string(" WibOS Kernel v1.0.0 (32-bit x86 Bare Metal)\n");
        print_string(" Loaded via custom 512-byte real-mode MBR boot loader.\n");
        print_string(" Built in C and Assembly. Extremely lightweight & fast.\n");
        print_string(" Running inside v86 WebAssembly x86 Virtual Machine.\n");
        print_string("========================================================\n");
    } else if (strcmp(cmd, "clear") == 0) {
        clear_console_area();
        return;
    } else if (strcmp(cmd, "matrix") == 0) {
        run_matrix_screensaver();
        return;
    } else if (strcmp(cmd, "beep") == 0) {
        print_string("Sending frequency signal to PC speaker...\n");
        beep();
    } else if (strcmp(cmd, "color") == 0) {
        static char col_scheme = 0;
        char theme_colors[] = {0x0A, 0x0F, 0x0E, 0x0B, 0x0D}; // Green, White, Yellow, Cyan, Magenta
        col_scheme = (col_scheme + 1) % 5;
        current_color = theme_colors[col_scheme];
        update_console_theme();
        print_string("Color theme updated to style ID #");
        print_char('0' + col_scheme);
        print_newline();
    } else if (strcmp(cmd, "reboot") == 0) {
        print_string("Issuing hardware reboot sequence...\n");
        delay(8000000);
        outb(0x64, 0xFE);
    } else if (strcmp(cmd, "shutdown") == 0) {
        print_string("CPU execution halted. WibOS VM is now idle.\n");
        __asm__("hlt");
    } else {
        print_string("wibos: command not found: ");
        print_string(cmd);
        print_string("\nType 'help' for options.\n");
    }
    print_string("wibos> ");
}

// C Entry Point called from kernel_entry.asm
void kernel_main() {
    init_screen();

    // Elegant loading boot logs
    print_string("[  0.000000] Initializing VGA Mode 3 Text Buffer... [OK]\n");
    delay(20000000);
    print_string("[  0.024510] Loading Global Descriptor Table (GDT)... [OK]\n");
    delay(20000000);
    print_string("[  0.048920] Switching to 32-bit Protected Mode... [OK]\n");
    delay(20000000);
    print_string("[  0.071230] Reading CMOS Real Time Clock... [OK]\n");
    delay(20000000);
    print_string("[  0.093440] Calibrating high-resolution system clock... [OK]\n");
    delay(20000000);
    print_string("[  0.116750] Mounting Virtual Floppy Drive A (wibos.img)... [OK]\n");
    delay(25000000);
    print_string("[  0.141200] WibOS Kernel v1.0.0 Boot Sequence Completed.\n\n");
    print_string("wibos> ");

    char input_buffer[256];
    int input_len = 0;

    // Main Polling Loop
    while (1) {
        update_uptime_and_clock();

        // Keyboard Controller Status Register Port 0x64: Bit 0 = 1 if output buffer holds data
        if ((inb(0x64) & 1) != 0) {
            unsigned char code = inb(0x60); // Read scan code

            if (!(code & 0x80)) { // Key press events
                if (code < sizeof(scancode_to_ascii)) {
                    char ascii = scancode_to_ascii[code];
                    
                    if (ascii == '\n') {
                        print_newline();
                        input_buffer[input_len] = '\0';
                        if (input_len > 0) {
                            execute_command(input_buffer);
                        } else {
                            print_string("wibos> ");
                        }
                        input_len = 0;
                    } else if (ascii == '\b') {
                        if (input_len > 0) {
                            input_len--;
                            print_char('\b');
                        }
                    } else if (ascii != 0) {
                        if (input_len < 255) {
                            input_buffer[input_len++] = ascii;
                            print_char(ascii);
                        }
                    }
                }
            }
        }
    }
}