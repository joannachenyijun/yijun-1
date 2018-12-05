create table account(
    username varchar(30) NOT NULL PRIMARY KEY,
    nickname varchar(30) NOT NULL,
    email varchar(40) NOT NULL,
    fname varchar(30) NOT NULL,
    lname varchar(30) NOT NULL,
    password varchar (30) NOT NULL,
    gender varchar(20),
    dob date
);


create table message(
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    subject varchar(200),
    comment varchar(2000),
    date_time DATETIME,
    isRead BOOLEAN,
    username_from varchar(30),
    username_to varchar(30),
    FOREIGN KEY (username_from) REFERENCES account (username),
    FOREIGN KEY (username_to) REFERENCES account (username)
);


create table contact(
    username_1 varchar(30) NOT NULL,
    username_2 varchar(30) NOT NULL,
    PRIMARY KEY (username_1, username_2),
    FOREIGN KEY (username_1) REFERENCES account (username),
    FOREIGN KEY (username_2) REFERENCES account (username)
);

create table friend(
    username_1 varchar(30) NOT NULL,
    username_2 varchar(30) NOT NULL,
    PRIMARY KEY (username_1, username_2),
    FOREIGN KEY (username_1) REFERENCES account (username),
    FOREIGN KEY (username_2) REFERENCES account (username)
);

create table block(
    username_1 varchar(30) NOT NULL,
    username_2 varchar(30) NOT NULL,
    PRIMARY KEY (username_1, username_2),
    FOREIGN KEY (username_1) REFERENCES account (username),
    FOREIGN KEY (username_2) REFERENCES account (username)
);


create table media (
    file_path varchar(700) NOT NULL PRIMARY KEY,
    username varchar(40),
    filename varchar(500),
    title varchar(500),
    tags varchar(500),
    description varchar(5000),
    date_time DATETIME,
    type varchar(30),
    size int,
    category varchar(30),
    rating float,
    allow_comment BOOLEAN DEFAULT true,
    allow_rate BOOLEAN DEFAULT true,
    share_type int,
    view_count int DEFAULT 0,
    download_count int  DEFAULT 0,
    FOREIGN KEY (username) REFERENCES account (username)
);


create table media_shared(
    share_id int NOT NULL AUTO_INCREMENT,
    file_path varchar(200) NOT NULL,
    username_from varchar(30) NOT NULL,
    username_to varchar(30) NOT NULL,
    date_time DATETIME NOT NULL,
    PRIMARY KEY (share_id),
    FOREIGN KEY (username_from) REFERENCES account (username),
    FOREIGN KEY (username_to) REFERENCES account (username)
);



CREATE TABLE download_blocks(
    file_path varchar(200) NOT NULL,
    blocked_by varchar(30) NOT NULL,
    blocked_username varchar(30) NOT NULL,
    PRIMARY KEY (file_path, blocked_by, blocked_username),
    FOREIGN KEY (blocked_by) REFERENCES account (username),
    FOREIGN KEY (blocked_username) REFERENCES account (username),
    FOREIGN KEY (file_path) REFERENCES media (file_path)
);


CREATE TABLE view_blocks(
    file_path varchar(200) NOT NULL,
    blocked_by varchar(30) NOT NULL,
    blocked_username varchar(30) NOT NULL,
    PRIMARY KEY (file_path, blocked_by, blocked_username),
    FOREIGN KEY (blocked_by) REFERENCES account (username),
    FOREIGN KEY (blocked_username) REFERENCES account (username),
    FOREIGN KEY (file_path) REFERENCES media (file_path)
);


CREATE TABLE media_keywords(
    file_path varchar(200) NOT NULL PRIMARY KEY,
    keywords varchar(7000) NOT NULL
);


CREATE TABLE media_view(
    view_id int AUTO_INCREMENT PRIMARY KEY,
    file_path varchar(200) NOT NULL,
    username varchar(30) NOT NULL,
    ip varchar(128) NOT NULL,
    date_time DATETIME NOT NULL,
    FOREIGN KEY (file_path) REFERENCES media (file_path)
);

CREATE TABLE media_download(
    download_id int AUTO_INCREMENT PRIMARY KEY,
    file_path varchar(200) NOT NULL,
    username varchar(30) NOT NULL,
    ip varchar(128) NOT NULL,
    date_time DATETIME NOT NULL,
    FOREIGN KEY (file_path) REFERENCES media (file_path),
    FOREIGN KEY (username) REFERENCES account (username)
);


CREATE TABLE playlist(
    playlist_id int(225) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    playlist_name varchar(20) NOT NULL,
    username varchar(30) NOT NULL,
    file_path varchar(200) NOT NULL,
    FOREIGN KEY (username) REFERENCES account (username)
);


CREATE TABLE subscribe(
    channel_name varchar(50) NOT NULL,
    username varchar(30) NOT NULL,
    PRIMARY KEY (channel_name,username),
    FOREIGN KEY (username) REFERENCES account (username)
);


CREATE TABLE channels(
    channel_name varchar(50) NOT NULL,
    file_path varchar(200) NOT NULL,
    PRIMARY KEY (file_path, channel_name),
    FOREIGN KEY (file_path) REFERENCES media (file_path)
);



CREATE TABLE favorites (
    favorite_id int(225) NOT NULL AUTO_INCREMENT,
    file_path varchar(200) NOT NULL,
    username varchar(30) NOT NULL,
    PRIMARY KEY (favorite_id),
    FOREIGN KEY (username) REFERENCES account (username),
    FOREIGN KEY (file_path) REFERENCES media (file_path)
);


CREATE TABLE rating(
    rating_id int(225) NOT NULL AUTO_INCREMENT,
    file_path varchar(200) NOT NULL,
    username varchar(30) NOT NULL,
    rating int(5) NOT NULL,
    PRIMARY KEY (rating_id),
    FOREIGN KEY (username) REFERENCES account (username),
    FOREIGN KEY (file_path) REFERENCES media (file_path)
);


CREATE TABLE  comments (
    comment_id int(225) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    file_path varchar(200) NOT NULL,
    comment varchar(5000) NOT NULL,
    username varchar(30) NOT NULL,
    date_time DATETIME NOT NULL,
    FOREIGN KEY (username) REFERENCES account (username),
    FOREIGN KEY (file_path) REFERENCES media (file_path)
);


CREATE TABLE groups (
    group_id int(225) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    group_name varchar(50) NOT NULL,
    master varchar(30) NOT NULL,
    total_topics int(225) NOT NULL DEFAULT 0,
    FOREIGN KEY (master) REFERENCES account (username)
);


CREATE TABLE group_users (
    group_id int(225) NOT NULL,
    username varchar(30) NOT NULL, -- include masters
    PRIMARY KEY (group_id, username),
    FOREIGN KEY (group_id) REFERENCES groups (group_id),
    FOREIGN KEY (username) REFERENCES account (username)
);


CREATE TABLE group_topics (
    topic_id int(225) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    topic_name varchar(200) NOT NULL,
    master varchar(30) NOT NULL,
    group_id int(225) NOT NULL,
    file_path varchar(200) NOT NULL,
    date_time DATETIME,
    FOREIGN KEY (group_id) REFERENCES groups (group_id),
    FOREIGN KEY (file_path) REFERENCES media (file_path),
    FOREIGN KEY (master) REFERENCES account (username)
);


CREATE TABLE group_discussion (
    discussion_id int(225) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    topic_id int(225) NOT NULL,
    username varchar(30) NOT NULL,
    comment varchar(5000) NOT NULL,
    date_time DATETIME NOT NULL,
    FOREIGN KEY (username) REFERENCES account (username),
    FOREIGN KEY (topic_id) REFERENCES group_topics (topic_id)
);
