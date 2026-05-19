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
echo -e '\033[01;32mCONNECTED TO NAMESERVER:\033[00m \033[01;33mlocalhost\033[00m'
echo -e '\033[01;32mDNS RESOLVER OPTION:    \033[00m \033[01;33mlocal\033[00m'
echo -e '\033[01;32mSTATUS:                 \033[00m \033[01;35mACTIVE (SECURE LINK)\033[00m'
echo -e '\033[01;34m----------------------------------------------------\033[00m'
export PS1='\[\033[01;32m\]demonperosn@wibscreen-vm\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]$ '
export DB_HOST=db
export DB_PORT=3306
export DB_DATABASE=Wibscreen
export DB_USERNAME=laravel_user
export DB_PASSWORD=1234
export REDIS_HOST=redis
export RABBITMQ_HOST=rabbitmq
export RABBITMQ_PORT=5672
export RABBITMQ_USER=guest
export RABBITMQ_PASSWORD=guest
