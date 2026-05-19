#!/bin/bash
# Get the username and password from ttyd HTTP headers (HTTP_X_TERMINAL_USER / HTTP_X_TERMINAL_PASSWORD)
USER_NAME="${HTTP_X_TERMINAL_USER:-$1}"
USER_NAME="${USER_NAME:-wibuser}"
USER_PASSWORD="${HTTP_X_TERMINAL_PASSWORD:-wibpass}"

# Sanitize username (alphanumeric only, lowercase, max 32 chars)
USER_NAME=$(echo "$USER_NAME" | tr -cd 'a-zA-Z0-9' | tr 'A-Z' 'a-z' | cut -c1-32)

# If username is empty after sanitization, fallback to "wibuser"
if [ -z "$USER_NAME" ]; then
    USER_NAME="wibuser"
fi

# Check if the user already exists in the container
if ! id "$USER_NAME" &>/dev/null; then
    # Create the user with a home directory and bash shell, added to the wheel group
    useradd -G wheel -m -s /bin/bash "$USER_NAME"
else
    # Ensure they are in the wheel group
    usermod -aG wheel "$USER_NAME" &>/dev/null || addgroup "$USER_NAME" wheel &>/dev/null || true
fi

# Set the user's password
echo "${USER_NAME}:${USER_PASSWORD}" | chpasswd &>/dev/null || true

# Set up passwordless sudo for the user
echo "$USER_NAME ALL=(ALL) NOPASSWD:ALL" > "/etc/sudoers.d/$USER_NAME"
chmod 0440 "/etc/sudoers.d/$USER_NAME"


# Ensure user prompt is configured and loaded on login
cat << 'EOF' > "/home/${USER_NAME}/.bashrc"
# Clear the terminal for a clean workspace look
clear

echo -e "\033[01;34m====================================================\033[00m"
echo -e "\033[01;36m    __      __.__ ___.                                 \033[00m"
echo -e "\033[01;36m   /  \    /  \__|\_ |__   ______ _________   ____   __\033[00m"
echo -e "\033[01;36m   \   \/\/   /  | | __ \ /  ___// ___\_  __ \_/ __ \ /  /\033[00m"
echo -e "\033[01;36m    \        /|  | | \_\ \\___ \\  \___|  | \/\  ___/  / \033[00m"
echo -e "\033[01;36m     \__/\  / |__| |___  /____  >\___  >__|    \___  >/__/\033[00m"
echo -e "\033[01;36m          \/           \/     \/     \/            \/   \033[00m"
echo -e "\033[01;34m====================================================\033[00m"
EOF

# Append nameserver and dns_option details if provided
if [ -n "$2" ]; then
    DNS_OPT="${3:-custom}"
    echo "echo -e '\033[01;32mCONNECTED TO NAMESERVER:\033[00m \033[01;33m$2\033[00m'" >> "/home/${USER_NAME}/.bashrc"
    echo "echo -e '\033[01;32mDNS RESOLVER OPTION:    \033[00m \033[01;33m$DNS_OPT\033[00m'" >> "/home/${USER_NAME}/.bashrc"
    echo "echo -e '\033[01;32mSTATUS:                 \033[00m \033[01;35mACTIVE (SECURE LINK)\033[00m'" >> "/home/${USER_NAME}/.bashrc"
    echo "echo -e '\033[01;34m----------------------------------------------------\033[00m'" >> "/home/${USER_NAME}/.bashrc"
else
    echo "echo -e '\033[01;32mSYSTEM READY:           \033[00m \033[01;33mLocal Linux VM Console\033[00m'" >> "/home/${USER_NAME}/.bashrc"
    echo "echo -e '\033[01;34m----------------------------------------------------\033[00m'" >> "/home/${USER_NAME}/.bashrc"
fi

echo "export PS1='\[\033[01;32m\]${USER_NAME}@wibscreen-vm\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ '" >> "/home/${USER_NAME}/.bashrc"

# Add network service connection details as environment variables for CLI integrations
{
    echo "export DB_HOST=db"
    echo "export DB_PORT=3306"
    echo "export DB_DATABASE=wibscreen_${USER_NAME}"
    echo "export DB_USERNAME=${USER_NAME}"
    echo "export DB_PASSWORD=${USER_PASSWORD}"
    echo "export REDIS_HOST=redis"
    echo "export RABBITMQ_HOST=rabbitmq"
    echo "export RABBITMQ_PORT=5672"
    echo "export RABBITMQ_USER=guest"
    echo "export RABBITMQ_PASSWORD=guest"
} >> "/home/${USER_NAME}/.bashrc"

echo "if [ -f ~/.bashrc ]; then . ~/.bashrc; fi" > "/home/${USER_NAME}/.bash_profile"
echo "if [ -f ~/.bashrc ]; then . ~/.bashrc; fi" > "/home/${USER_NAME}/.profile"

# Ensure ownership is correct
chown -R "${USER_NAME}:${USER_NAME}" "/home/${USER_NAME}"

# Secure permissions to isolate users:
# 1. Set home directory permissions to 700 so only the owner can read/write/access it
chmod 700 "/home/${USER_NAME}"

# 2. Set /home permissions to 711 so users cannot list other directories but can traverse to their own
chmod 711 /home

# Switch to the user and start the interactive bash shell in their home directory
exec su - "$USER_NAME"
