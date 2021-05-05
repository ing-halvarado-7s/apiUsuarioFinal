sudo find . -type f -exec chmod 644 {} \;
sudo find . -type d -exec chmod 755 {} \;
sudo chgrp -R $USER storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache