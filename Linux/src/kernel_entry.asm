; WibOS Kernel Entry
; Transitions segment registers to 32-bit PM data descriptors, initializes stack, and enters C.
[bits 32]
[extern kernel_main]

global _start
_start:
    ; Set segment registers to our 32-bit GDT Data Selector (0x10)
    mov ax, 0x10
    mov ds, ax
    mov es, ax
    mov fs, ax
    mov gs, ax
    mov ss, ax

    ; Initialize stack pointer
    mov esp, 0x90000

    ; Call our main C entry point
    call kernel_main

    ; Hang if the kernel returns
    cli
.hang:
    hlt
    jmp .hang
