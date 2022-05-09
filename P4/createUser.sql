CREATE USER 'admin'@'%' IDENTIFIED BY 'SIBW';
GRANT create, delete, drop, index, insert, select, update ON SIBW.* TO ''@'%';