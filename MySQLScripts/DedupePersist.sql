USE twitterfeed;
CREATE TABLE n_persist as
SELECT * FROM persist WHERE 1 GROUP BY max_id;
DROP TABLE persist;
RENAME TABLE n_persist TO persist;


