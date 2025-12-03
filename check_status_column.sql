-- statusカラムの型を確認
SHOW COLUMNS FROM customers WHERE Field = 'status';

-- または
DESCRIBE customers status;

-- テーブル全体の構造を確認
SHOW CREATE TABLE customers;