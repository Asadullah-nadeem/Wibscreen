; WibOS 16-bit Bootloader
; Loads the 32-bit C kernel from floppy disk, switches to Protected Mode, and runs it.
[org 0x7c00]
[bits 16]

KERNEL_OFFSET equ 0x10000 ; Physical address 0x10000 where the kernel is loaded

start:
    ; Set up segment registers and stack pointer
    xor ax, ax
    mov ds, ax
    mov es, ax
    mov ss, ax
    mov sp, 0x7c00
    mov [boot_drive], dl     ; Save boot drive number provided by BIOS

    ; Clear the screen using BIOS interrupt
    mov ax, 0x03
    int 0x10

    ; Print booting status message
    mov si, msg_booting
    call print_string

    ; Introduce a 1.5-second BIOS delay (1,500,000 microseconds = 0x0016E360)
    ; to let the user clearly see the bootloader start.
    mov ah, 0x86
    mov cx, 0x0016
    mov dx, 0xE360
    int 0x15

    ; Load kernel from floppy disk
    mov bx, 0x1000           ; Load destination segment (ES:BX = 0x1000:0x0000 -> 0x10000 physical)
    mov es, bx
    xor bx, bx
    
    mov ah, 0x02             ; BIOS Read Sectors function
    mov al, 32               ; Read 32 sectors (16 KB, plenty for our small C kernel)
    mov ch, 0                ; Cylinder 0
    mov cl, 2                ; Sector 2 (Sector 1 is this bootloader)
    mov dh, 0                ; Head 0
    mov dl, [boot_drive]     ; Boot drive
    int 0x13
    jc disk_error            ; Carry flag set means disk error

    ; Disable interrupts before switching to Protected Mode
    cli

    ; Enable A20 gate via System Control Port A (Fast A20)
    in al, 0x92
    or al, 2
    out 0x92, al

    ; Load Global Descriptor Table (GDT)
    lgdt [gdt_descriptor]

    ; Switch to Protected Mode by setting Bit 0 of CR0
    mov eax, cr0
    or eax, 1
    mov cr0, eax

    ; Far jump to our 32-bit code descriptor segment to flush CPU pipeline
    jmp 0x08:init_pm

[bits 32]
init_pm:
    mov ax, 0x10             ; GDT data segment selector (0x10)
    mov ds, ax
    mov es, ax
    mov fs, ax
    mov gs, ax
    mov ss, ax
    mov esp, 0x90000         ; Set up stack pointer to safe high memory

    jmp KERNEL_OFFSET        ; Jump to kernel entry address (0x10000)

[bits 16]
; 16-bit Print String function
print_string:
    mov ah, 0x0e
.loop:
    lodsb
    test al, al
    jz .done
    int 0x10
    jmp .loop
.done:
    ret

disk_error:
    mov si, msg_error
    call print_string
    jmp $                    ; Infinite loop/hang

; Data variables
boot_drive db 0
msg_booting db "Loading MBR Boot Sector (16-bit Real Mode)... [OK]", 13, 10, "Entering 32-bit Protected Mode... [OK]", 13, 10, 0
msg_error db "Boot Error: Failed to read sectors from floppy!", 13, 10, 0

; Global Descriptor Table (GDT) setup
gdt_start:
    dd 0, 0                  ; Null descriptor (mandatory)
gdt_code:
    dw 0xffff                ; Limit (0-15) = 4GB (0xfffff)
    dw 0                     ; Base (0-15) = 0
    db 0                     ; Base (16-23) = 0
    db 10011010b             ; Access byte: Present, Privilege 0, Code, Readable
    db 11001111b             ; Flags: Granularity 4KB, 32-bit protected mode
    db 0                     ; Base (24-31) = 0
gdt_data:
    dw 0xffff                ; Limit (0-15) = 4GB
    dw 0                     ; Base (0-15) = 0
    db 0                     ; Base (16-23) = 0
    db 10010010b             ; Access byte: Present, Privilege 0, Data, Writable
    db 11001111b             ; Flags: Granularity 4KB, 32-bit protected mode
    db 0                     ; Base (24-31) = 0
gdt_end:

gdt_descriptor:
    dw gdt_end - gdt_start - 1 ; Size of GDT - 1
    dd gdt_start               ; Address of GDT

; Pad bootloader to 510 bytes, then add boot signature
times 510-($-$$) db 0
dw 0xaa55