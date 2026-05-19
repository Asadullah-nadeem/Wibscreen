FROM debian:stable-slim

# Install compilation toolchain: nasm, make, gcc, gcc-multilib (for 32-bit support), and binutils
RUN apt-get update && apt-get install -y --no-install-recommends \
    nasm \
    gcc \
    gcc-multilib \
    make \
    binutils \
    && rm -rf /var/lib/apt/lists/*

# Set up build directory
WORKDIR /build

# Default action is to run make to build wibos.img
CMD ["make"]
