// Native WebAssembly C Code Example
// File: add.c

// To compile this into math.wasm, you would typically run a compiler like Clang or Emscripten:
// clang --target=wasm32 -nostdlib -Wl,--no-entry -Wl,--export-all -o math.wasm add.c

// This is pure C code that bypasses operating systems entirely.
int add(int a, int b) {
    return a + b;
}

// A more complex mathematical function to demonstrate the raw native speed of WebAssembly.
int calculate_speed(int loops) {
    int x = 0;
    for (int i = 0; i < loops; i++) {
        x += (i * 2);
    }
    return x;
}
