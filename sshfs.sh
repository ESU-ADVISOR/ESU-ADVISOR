
if [ -d "./ssh-fs" ]; then
    echo "Folder exists"
    fusermount -u ./ssh-fs
    rm -rf ./ssh-fs
else
    echo "Folder does not exist"
fi

mkdir ./ssh-fs

echo "Folder created, initiating connection..."

sshfs tecweb:/home/mvasquez ./ssh-fs
