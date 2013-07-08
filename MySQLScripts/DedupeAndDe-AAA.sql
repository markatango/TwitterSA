USE twitterfeed;
CREATE TABLE n_tweetdata as
SELECT * FROM tweetdata WHERE 1 GROUP BY id_str;
DROP TABLE tweetdata;
RENAME TABLE n_tweetdata TO tweetdata;
SELECT * FROM tweetdata WHERE text NOT RLIKE '^RT' AND user NOT RLIKE 'AAA' AND text NOT RLIKE '^AAA';

