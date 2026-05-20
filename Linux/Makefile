# Makefile for compiling WibOS Custom 32-bit Kernel and Bootloader
# Targets standard x86 compilation tools inside the Docker build container.

AS = nasm
CC = gcc
LD = ld
OBJCOPY = objcopy

SRC_DIR = src
BUILD_DIR = .

all: wibos.img

wibos.img: boot.bin kernel.bin
	cat boot.bin kernel.bin > temp_wibos.img
	# Create a standard 1.44MB blank floppy disk image (1440 KB)
	dd if=/dev/zero of=wibos.img bs=1024 count=1440
	# Write the compiled bootloader + kernel onto the floppy image without truncating
	dd if=temp_wibos.img of=wibos.img conv=notrunc
	# Copy to the wibos/ subdirectory if it exists
	mkdir -p wibos && cp wibos.img wibos/wibos.img
	rm -f temp_wibos.img boot.bin kernel.bin kernel_entry.o kernel.o
	@echo "WibOS floppy image wibos.img compiled and generated successfully!"

boot.bin: $(SRC_DIR)/boot.asm
	$(AS) -f bin $< -o $@

kernel.bin: kernel_entry.o kernel.o
	$(LD) -m elf_i386 -T $(SRC_DIR)/linker.ld kernel_entry.o kernel.o -o kernel.elf
	$(OBJCOPY) -O binary kernel.elf $@
	rm kernel.elf

kernel_entry.o: $(SRC_DIR)/kernel_entry.asm
	$(AS) -f elf32 $< -o $@

kernel.o: $(SRC_DIR)/kernel.c
	$(CC) -m32 -ffreestanding -fno-pie -fno-stack-protector -nostdlib -c $< -o $@

clean:
	rm -f *.o *.bin *.elf wibos.img

.PHONY: all clean
