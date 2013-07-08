USE twitterfeed;
CREATE TABLE persist as
SELECT * FROM persist WHERE 1 GROUP BY max_id;
DROP TABLE persist;
RENAME TABLE n_table TO persist;
SELECT * FROM persist;

