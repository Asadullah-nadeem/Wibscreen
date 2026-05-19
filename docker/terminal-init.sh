#!/bin/bash
# Get the username from argument, default to "wibuser" if not provided
USER_NAME="${1:-wibuser}"

# Sanitize username (alphanumeric only, lowercase, max 32 chars)
USER_NAME=$(echo "$USER_NAME" | tr -cd 'a-zA-Z0-9' | tr 'A-Z' 'a-z' | cut -c1-32)

# If username is empty after sanitization, fallback to "wibuser"
if [ -z "$USER_NAME" ]; then
    USER_NAME="wibuser"
fi

# Check if the user already exists in the container
if ! id "$USER_NAME" &>/dev/null; then
    # Create the user with a home directory and bash shell
    useradd -m -s /bin/bash "$USER_NAME"
    
    # Configure user prompt (PS1) to look premium and show the customized user shell
    echo "export PS1='\[\033[01;32m\]${USER_NAME}@wibscreen-vm\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ '" >> "/home/${USER_NAME}/.bashrc"
    
    # Ensure ownership is correct
    chown -R "${USER_NAME}:${USER_NAME}" "/home/${USER_NAME}"
fi

# Switch to the user and start the interactive bash shell in their home directory
exec su - "$USER_NAME"
