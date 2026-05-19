/* WibOS Custom 32-bit Kernel in C */

#define SCREEN_WIDTH 80
#define SCREEN_HEIGHT 25
#define VIDEO_MEM ((volatile char*)0xB8000)

// VGA Cursor register ports
#define REG_SCREEN_CTRL 0x3D4
#define REG_SCREEN_DATA 0x3D5

// US Keyboard layout scancode to ASCII translation table (no shift handled for simplicity, basic lowercase/numbers)
static const char scancode_to_ascii[] = {
    0,   27,  '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '=', '\b',
    '\t', 'q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', '[', ']', '\n',
    0,   'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', ';', '\'', '`',
    0,   '\\', 'z', 'x', 'c', 'v', 'b', 'n', 'm', ',', '.', '/', 0,
    '*', 0,   ' '
};

// Global variables for cursor position and text color
static int cursor_x = 0;
static int cursor_y = 0;
static char current_color = 0x0A; // Bright Green on Black (Matrix style default)

// Forward declarations
unsigned char inb(unsigned short port);
void outb(unsigned short port, unsigned char value);
void update_cursor();
void clear_screen();
void print_char(char c);
void print_string(const char* str);
void print_newline();
void scroll();
int strcmp(const char* s1, const char* s2);
int strlen(const char* str);
void execute_command(const char* cmd);
void run_matrix_screensaver();
void beep();

// I/O Port Helper Functions
unsigned char inb(unsigned short port) {
    unsigned char result;
    __asm__ volatile("inb %1, %0" : "=a" (result) : "Nd" (port));
    return result;
}

void outb(unsigned short port, unsigned char value) {
    __asm__ volatile("outb %0, %1" : : "a" (value), "Nd" (port));
}

// Update the VGA hardware cursor position
void update_cursor() {
    unsigned short position = cursor_y * SCREEN_WIDTH + cursor_x;
    outb(REG_SCREEN_CTRL, 14);
    outb(REG_SCREEN_DATA, (unsigned char)(position >> 8));
    outb(REG_SCREEN_CTRL, 15);
    outb(REG_SCREEN_DATA, (unsigned char)(position & 0xFF));
}

// Clear the video text buffer
void clear_screen() {
    for (int i = 0; i < SCREEN_WIDTH * SCREEN_HEIGHT; i++) {
        VIDEO_MEM[i * 2] = ' ';
        VIDEO_MEM[i * 2 + 1] = current_color;
    }
    cursor_x = 0;
    cursor_y = 0;
    update_cursor();
}

// Print single character on screen
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

// Move to start of next line
void print_newline() {
    cursor_x = 0;
    cursor_y++;
    if (cursor_y >= SCREEN_HEIGHT) {
        scroll();
    }
    update_cursor();
}

// Scroll terminal contents up 1 line
void scroll() {
    for (int y = 1; y < SCREEN_HEIGHT; y++) {
        for (int x = 0; x < SCREEN_WIDTH; x++) {
            int dest = ((y - 1) * SCREEN_WIDTH + x) * 2;
            int src = (y * SCREEN_WIDTH + x) * 2;
            VIDEO_MEM[dest] = VIDEO_MEM[src];
            VIDEO_MEM[dest + 1] = VIDEO_MEM[src + 1];
        }
    }
    for (int x = 0; x < SCREEN_WIDTH; x++) {
        int offset = ((SCREEN_HEIGHT - 1) * SCREEN_WIDTH + x) * 2;
        VIDEO_MEM[offset] = ' ';
        VIDEO_MEM[offset + 1] = current_color;
    }
    cursor_y = SCREEN_HEIGHT - 1;
    update_cursor();
}

// Safe string comparison
int strcmp(const char* s1, const char* s2) {
    while (*s1 && (*s1 == *s2)) {
        s1++;
        s2++;
    }
    return *(const unsigned char*)s1 - *(const unsigned char*)s2;
}

// String length helper
int strlen(const char* str) {
    int len = 0;
    while (str[len] != '\0') len++;
    return len;
}

// Beep the PC speaker
void beep() {
    unsigned int freq = 800; // Frequency in Hz
    unsigned int div = 1193180 / freq;
    
    // Set 8253 PIT command register (Channel 2, Mode 3 square wave generator)
    outb(0x43, 0xB6);
    outb(0x42, (unsigned char)(div & 0xFF));
    outb(0x42, (unsigned char)((div >> 8) & 0xFF));

    // Turn PC Speaker on (Bit 0 and 1 of System Control Port B)
    unsigned char speaker = inb(0x61);
    outb(0x61, speaker | 0x03);

    // Simple busy-loop delay
    for (volatile int d = 0; d < 12000000; d++);

    // Turn PC Speaker off
    outb(0x61, inb(0x61) & 0xFC);
}

// Falling Matrix Code screensaver animation
void run_matrix_screensaver() {
    clear_screen();
    print_string("Initializing Matrix Digital Rain... Press ESC to return.\n");
    for (volatile int d = 0; d < 20000000; d++);
    
    clear_screen();
    
    int columns[SCREEN_WIDTH];
    for (int i = 0; i < SCREEN_WIDTH; i++) {
        columns[i] = -(i % 17); // Stagger start offsets
    }
    
    unsigned int random_seed = 98765;
    int is_running = 1;

    while (is_running) {
        for (int x = 0; x < SCREEN_WIDTH; x++) {
            int y = columns[x];
            
            // Clear trail tail character
            if (y - 8 >= 0 && y - 8 < SCREEN_HEIGHT) {
                int offset = ((y - 8) * SCREEN_WIDTH + x) * 2;
                VIDEO_MEM[offset] = ' ';
                VIDEO_MEM[offset + 1] = 0x07;
            }
            
            // Draw green trail with bright white head
            for (int t = 0; t < 8; t++) {
                int cy = y - t;
                if (cy >= 0 && cy < SCREEN_HEIGHT) {
                    int offset = (cy * SCREEN_WIDTH + x) * 2;
                    random_seed = random_seed * 1103515245 + 12345;
                    char rand_char = 33 + (random_seed % 93); // Printable ASCII chars
                    VIDEO_MEM[offset] = rand_char;
                    
                    // Colors: head is bright white, tail fades out
                    if (t == 0) {
                        VIDEO_MEM[offset + 1] = 0x0F; // Bright White
                    } else if (t < 3) {
                        VIDEO_MEM[offset + 1] = 0x0A; // Bright Green
                    } else {
                        VIDEO_MEM[offset + 1] = 0x02; // Dark Green
                    }
                }
            }
            
            columns[x]++;
            if (columns[x] >= SCREEN_HEIGHT + 8) {
                columns[x] = 0;
            }
        }
        
        // Control rain speed
        for (volatile int d = 0; d < 1800000; d++);

        // Check if ESC is pressed (Scan code 0x01)
        if ((inb(0x64) & 1) != 0) {
            unsigned char code = inb(0x60);
            if (code == 0x01 || code == 0x10) { // ESC or 'q' scan code
                is_running = 0;
            }
        }
    }
    
    current_color = 0x0A; // Reset to Matrix theme
    clear_screen();
    print_string("WibOS Console restored.\n");
    print_string("wibos> ");
}

// Shell Command Evaluator
void execute_command(const char* cmd) {
    if (strcmp(cmd, "help") == 0) {
        print_string("WibOS Shell Commands:\n");
        print_string("  help     - Display this command dictionary\n");
        print_string("  about    - Print operating system & bootloader version details\n");
        print_string("  clear    - Clear console screen buffer\n");
        print_string("  matrix   - Launch bare-metal falling digital code rain screensaver\n");
        print_string("  beep     - Play pitch-controlled beep tone via PC Speaker\n");
        print_string("  color    - Cycle terminal layout background and text color themes\n");
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
        clear_screen();
        return; // Don't print prompt here, print it in calling shell loop to keep clean
    } else if (strcmp(cmd, "matrix") == 0) {
        run_matrix_screensaver();
        return; // Prompt is handled by the screensaver exit routine
    } else if (strcmp(cmd, "beep") == 0) {
        print_string("Sending frequency signal to PC speaker...\n");
        beep();
    } else if (strcmp(cmd, "color") == 0) {
        static char col_scheme = 0;
        char theme_colors[] = {0x0A, 0x0F, 0x0E, 0x0B, 0x0D}; // Green, White, Yellow, Cyan, Magenta
        col_scheme = (col_scheme + 1) % 5;
        current_color = theme_colors[col_scheme];
        clear_screen();
        print_string("Color scheme updated to style ID #");
        print_char('0' + col_scheme);
        print_newline();
    } else if (strcmp(cmd, "reboot") == 0) {
        print_string("Issuing hardware reboot sequence...\n");
        for (volatile int d = 0; d < 8000000; d++);
        // Pulse keyboard controller reset pin (Standard x86 warm reboot trigger)
        outb(0x64, 0xFE);
    } else if (strcmp(cmd, "shutdown") == 0) {
        print_string("CPU execution halted. WibOS VM is now idle.\n");
        __asm__("hlt");
    } else {
        print_string("wibos: command not found: ");
        print_string(cmd);
        print_string("\nType 'help' for command options.\n");
    }
    print_string("wibos> ");
}

// C Entry Point called from kernel_entry.asm
void kernel_main() {
    clear_screen();
    print_string("========================================================\n");
    print_string("     Welcome to WibOS Custom 32-bit x86 Operating System\n");
    print_string("========================================================\n");
    print_string("Boot Sequence Completed. CPU Mode: 32-Bit Protected Mode\n");
    print_string("Host Environment: x86 WebAssembly VM | Speed: Instant\n");
    print_string("Type 'help' to display system command tools.\n\n");
    print_string("wibos> ");

    char input_buffer[256];
    int input_len = 0;

    // Main Keyboard polling Event Loop
    while (1) {
        // Poll Port 0x64: Keyboard Controller Status Register
        // Bit 0 = 1 if output buffer holds data from keyboard scan code
        if ((inb(0x64) & 1) != 0) {
            unsigned char code = inb(0x60); // Read scan code from Input Buffer (Port 0x60)

            // Scancodes above 0x80 are key release events. We only want key press (bit 7 clear)
            if (!(code & 0x80)) {
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