DROP DATABASE IF EXISTS QuizIT;
CREATE DATABASE IF NOT EXISTS QuizIT;
USE QuizIT;

CREATE TABLE IF NOT EXISTS User
(
    PK_userID INT PRIMARY KEY AUTO_INCREMENT,
    username  VARCHAR(30),
    password  CHAR(60),
    role      VARCHAR(10)
);

CREATE TABLE IF NOT EXISTS Topic
(
    PK_topicID INT PRIMARY KEY,
    topicname  VARCHAR(80)
);

CREATE TABLE IF NOT EXISTS User_Topic
(
    FK_PK_userID  INT,
    FK_PK_topicID INT,
    unlocked      BOOLEAN,
    completed     BOOLEAN,
    CONSTRAINT const_user_topic_userID FOREIGN KEY (FK_PK_userID) REFERENCES User (PK_userID),
    CONSTRAINT const_user_topic_topicID FOREIGN KEY (FK_PK_topicID) REFERENCES Topic (PK_topicID)
);

CREATE TABLE IF NOT EXISTS Quiz
(
    PK_quizID     INT PRIMARY KEY,
    title         VARCHAR(80),
    difficulty    INT,
    FK_PK_topicID INT,
    CONSTRAINT const_quiz_topicID FOREIGN KEY (FK_PK_topicID) REFERENCES Topic (PK_topicID)
);

CREATE TABLE IF NOT EXISTS Statistic
(
    PK_statisticID INT PRIMARY KEY AUTO_INCREMENT,
    score          INT NOT NULL,
    date           DATETIME,
    FK_PK_quizID   INT,
    FK_PK_userID   INT,
    CONSTRAINT const_statistic_quizID FOREIGN KEY (FK_PK_quizID) REFERENCES Quiz (PK_quizID),
    CONSTRAINT const_statistic_userID FOREIGN KEY (FK_PK_userID) REFERENCES User (PK_userID)
);

CREATE TABLE IF NOT EXISTS Question
(
    PK_questionID INT PRIMARY KEY,
    text          VARCHAR(300),
    type          VARCHAR(10),
    FK_PK_quizID  INT,
    CONSTRAINT const_question_quizID FOREIGN KEY (FK_PK_quizID) REFERENCES Quiz (PK_quizID)
);

CREATE TABLE IF NOT EXISTS Answer
(
    PK_answerID      INT PRIMARY KEY,
    text             VARCHAR(300),
    correct          BOOLEAN,
    FK_PK_questionID INT,
    CONSTRAINT const_answer_questionID FOREIGN KEY (FK_PK_questionID) REFERENCES Question (PK_questionID)
);

INSERT INTO User (PK_userID, username, password, role)
VALUES (1, 'admin', '$2y$10$PyWF/NMew/Kn809GyVHr0.5Ywlw.34yEZzfyqjGylmTABItSeE/4i', 'admin'),
       (2, 'user', '$2y$10$XLmB78BFGYLk3V0OPXg1Keg.4tf.toxVWwJhPUd5hXWGIrerdb5xO', 'user');

INSERT INTO Topic (pk_topicID, topicname)
VALUES (1, 'Linux Networking'),
       (2, 'Cisco Routing'),
       (3, 'Cisco Switching'),
       (4, 'Windows-Linux File Sharing (FTP/SMB)'),
       (5, 'Network Address Translation (SNAT/DNAT/PAT)');

INSERT INTO User_Topic (FK_PK_userID, FK_PK_topicID, unlocked, completed)
VALUES (1, 1, TRUE, TRUE),
       (1, 2, TRUE, FALSE),
       (1, 3, TRUE, FALSE),
       (1, 4, TRUE, FALSE),
       (1, 5, TRUE, FALSE),
       (2, 1, FALSE, FALSE),
       (2, 2, TRUE, TRUE),
       (2, 3, TRUE, TRUE),
       (2, 4, FALSE, FALSE),
       (2, 5, FALSE, FALSE);

INSERT INTO Quiz (PK_quizID, title, difficulty, FK_PK_topicID)
VALUES (1, 'Volatile Network Configuration', 1, 1),
       (2, 'Persistent Network Configuration', 3, 1),
       (3, 'DHCP Pools', 2, 2),
       (4, 'Router-On-A-Stick (ROAS)', 2, 2),
       (5, 'Static Routing', 3, 2),
       (6, 'Dynamic Routing', 1, 2),
       (7, 'VLANs', 2, 3),
       (8, 'Loop Prevention Protocols (STP/PSVST/PSVST+/MVSTP/RSTP)', 2, 3),
       (9, 'Samba File Sharing (SMB)', 2, 4),
       (10, 'Secure File Transfer Protocol (SFTP)', 3, 4),
       (11, 'Static Network Address Translation (SNAT)', 1, 5),
       (12, 'Dynamic Network Address Translation (DNAT)', 2, 5),
       (13, 'Port Address Translation (PAT)', 1, 5);

INSERT INTO Question (PK_questionID, text, type, FK_PK_quizID)
VALUES (1, 'What is the shortest command to display the current ip address configuration with colors in Linux?', 'text',
        1),
       (2, 'True or false: "ip link set dev ens33 up" sets the interface ens33 down.', 'radio', 1),
       (3, 'Which ways are there to view the active network configuration?', 'checkbox', 1),
       (4,
        'Specify the absolute path of the file that must be edited to make the network configuration persistent in Linux Ubuntu/Debian distributions?',
        'text', 2),
       (5,
        'True or false: The absolute path of the file that must be edited to make the network configuration persistent in Linux RedHat/Fedora/CentOS distributions is /etc/network-scripts/sysconfig/ifcfg-enXX',
        'radio', 2),
       (6, 'True or false: DHCP pools exist both for IPv4 and for IPv6', 'radio', 3),
       (7, 'Which phases are there in the dynamic address resolution through DHCP?', 'radio', 3),
       (8, 'True or false: A DHCP pool is used to assign static IP addresses.', 'radio', 3),
       (9, 'Which of these commands excludes the IP address range if 10.0.0.1 - 10.0.0.9 from being offered to hosts?',
        'radio', 3),
       (10,
        'Which of these commands creates a functioning DHCP pool on a Cisco router - offering IP addresses in the range of 192.168.0.1 - 192.168.0.254 and advertising 192.168.0.254 as DNS server and default-gateway?',
        'radio', 3),
       (11, 'True or false: A Router-On-A-Stick has a single IP address per interface.', 'radio', 4),
       (12, 'A Router-On-A-Stick is a simple way for small networks to implement inter-VLAN connectivity.', 'radio', 4),
       (13, 'How are interfaces used for ROAS configuration addressed?', 'radio', 4),
       (14, 'Which command successfully adds 10.20.30.40 as the gateway of last resort (default-gateway)?', 'radio', 5),
       (15, 'Add a static route to 240.128.0.0/12 through 192.168.10.254.', 'text', 5),
       (16,
        'True or false: First Hop Redundancy Protocols (FHRP) like HSRP add availability to the static default routes of a switch or hosts with in case of device failure.',
        'radio', 5),
       (17, 'Which protocols are there offering dynamic routing?', 'checkbox', 6),
       (18, 'Which RIP version should be used?', 'text', 6),
       (19, 'Where is RIP being configured correctly?', 'checkbox', 6),
       (20, 'Why have VLANs been invented?', 'checkbox', 7),
       (21, 'On which OSI layer do VLANs operate?', 'radio', 7),
       (22, 'Which two types of switchport modes are being differentiated?', 'checkbox', 7),
       (23, 'Why should VoIP communication have a separate VLAN?', 'radio', 7),
       (24, 'True or False: Spanning Tree Protocol (STP) is used to prevent layer 2 loops in a network.', 'radio', 8),
       (25, 'What are the main benefits of loop prevention protocols?', 'checkbox', 8),
       (26, 'Which Samba versions are there?', 'checkbox', 9),
       (27, 'What are the big benefits Samba 3.0 has to offer?', 'radio', 9),
       (28, 'What is Server Message Block (SMB) used for?', 'checkbox', 9),
       (29, 'Which Linux packages have to be downloaded to use Samba?', 'checkbox', 9),
       (30, 'Specify the absolute file path of the Samba configuration file.', 'text', 9),
       (31, 'How is the Samba server/client service activated on Windows?', 'radio', 9),
       (32,
        'Which of the following is a type of Network Address Translation (NAT)?',
        'radio', 11),
       (33, 'Why was NAT initially implemented?', 'radio', 11),
       (34, 'Which commands have to be issues on the local/global router interfaces to use any type of NAT?', 'radio',
        11),
       (35, 'Specify the full command to show all nat mappings (translations) in Cisco privileged exec mode.', 'text',
        11),
       (36, 'What is the difference between SNAT and DNAT?', 'checkbox', 12),
       (37,
        'True of false: The command "ip nat pool DNATPOOL 209.165.200.226 209.165.200.240 netmask 255.255.255.224" is a valid way to create a DNAT pool.',
        'radio', 12),
       (38, 'What will happen, if the address pool of DNAT is exhausted?', 'radio', 12),
       (39, 'True or False: Port Address Translation (PAT) supports the use of address pools.', 'radio', 13),
       (40, 'With which keyword at the end of "ip nat inside source list ACL interface INT" can PAT be specified?',
        'text', 13),
       (41, 'For SFTP, which way is traffic secured?', 'checkbox', 10)
;

INSERT INTO Answer (PK_answerID, text, correct, FK_PK_questionID)
VALUES (1, 'ip -c a', TRUE, 1),
       (2, 'False', TRUE, 2),
       (3, 'True', FALSE, 2),
       (4, 'cat /etc/network/interfaces', TRUE, 3),
       (5, 'cat /etc/sysconfig/network-scripts/ifcfg-enXX', TRUE, 3),
       (6, 'ip link show', TRUE, 3),
       (7, 'ip address', TRUE, 3),
       (8, 'show ip int br', FALSE, 3),
       (9, 'cat /config/network/ipv4', FALSE, 3),
       (10, '/etc/network/interfaces', TRUE, 4),
       (11, 'False', TRUE, 5),
       (12, 'True', FALSE, 5),
       (13, 'True', TRUE, 6),
       (14, 'False', FALSE, 6),
       (15, 'Discover -> Offer -> Request -> Acknowledge', TRUE, 7),
       (16, 'Request -> Discover -> Acknowledge -> End', FALSE, 7),
       (17, 'Question -> Answer -> Allocation -> Check', FALSE, 7),
       (18, 'False', TRUE, 8),
       (19, 'True', FALSE, 8),
       (20, 'ip dhcp excluded-address 10.0.0.1 10.0.0.9', TRUE, 9),
       (21, 'ip dhcp excluded-address 10.0.0.0-10.0.0.9', FALSE, 9),
       (22, 'dhcp exclude addresses 10.0.0.1 to 10.0.0.9', FALSE, 9),
       (23, 'no ip dhcp included-address 10.0.0.1 10.0.0.9', FALSE, 9),
       (24, 'ip dhcp pool POOLNAME<br>&emsp;
                network 192.168.0.0 255.255.255.0<br>&emsp;
                default-router 192.168.0.254', TRUE, 10),
       (25, 'ip dhcp pool POOLNAME<br>&emsp;
                network 192.168.0.0 255.255.255.0<br>&emsp;
                ip default-gateway 192.168.0.254', FALSE, 10),
       (26, 'ip dhcp pool POOLNAME<br>&emsp;
                ip range 192.168.0.1 192.168.0.254<br>&emsp;
                default-router 192.168.0.254', FALSE, 10),
       (27, 'False', TRUE, 11),
       (28, 'True', FALSE, 11),
       (29, 'True', TRUE, 12),
       (30, 'False', FALSE, 12),
       (31, 'int g0/0/0.10', TRUE, 13),
       (32, 'int g0/0/0 vlan 10', FALSE, 13),
       (33, 'subint g0/0/0.10', FALSE, 13),
       (34, 'vlan 10 int g0/0/0', FALSE, 13),
       (35, 'ip route 0.0.0.0 0.0.0.0 10.20.30.40', TRUE, 14),
       (36, 'ip route add 0.0.0.0 0.0.0.0 10.20.30.40', FALSE, 14),
       (37, 'ip route default 10.20.30.40', FALSE, 14),
       (38, 'ip-default gateway 0.0.0.0 0.0.0.0 10.20.30.40', FALSE, 14),
       (39, 'ip route 240.128.0.0 255.240.0.0 192.168.10.254', TRUE, 15),
       (40, 'ip route 240.128.0.0/12 via 192.168.10.254', FALSE, 15),
       (41, 'True', TRUE, 16),
       (42, 'False', FALSE, 16),
       (43, 'RIP', TRUE, 17),
       (44, 'OSPF', TRUE, 17),
       (45, 'EIGRP', TRUE, 17),
       (46, 'BGP', TRUE, 17),
       (47, 'ICMP', FALSE, 17),
       (48, 'SSH', FALSE, 17),
       (49, '2', TRUE, 18),
       (50, 'router rip<br>&emsp;
                network 10.0.0.0 255.0.0.0<br>&emsp;
                passive-interface GigabitEthernet0/0/0', TRUE, 19),
       (51, 'router rip<br>&emsp;
                network 10.0.0.0 255.0.0.0<br>&emsp;
                default-information originate', TRUE, 19),
       (52, 'router rip<br>&emsp;
                network 10.0.0.0/8<br>&emsp;
                passive-interface GigabitEthernet0/0/0', FALSE, 19),
       (53, 'router bgp<br>&emsp;
                network 10.0.0.0 255.0.0.0<br>&emsp;
                passive-interface GigabitEthernet0/0/0', FALSE, 19),
       (54, 'To offer separate management and security zones.', TRUE, 20),
       (55, 'In order to decrease the size of broadcast domains which improves network performance.', TRUE, 20),
       (56, 'Because VLANs are a necessary for basic functionality.', FALSE, 20),
       (57, 'For VLANs offer data encryption.', FALSE, 20),
       (58, 'Layer 2 - data link', TRUE, 21),
       (59, 'Layer 1 - physical', FALSE, 21),
       (60, 'Layer 3 - network', FALSE, 21),
       (61, 'Layer 4 - transport', FALSE, 21),
       (62, 'Layer 5 - session', FALSE, 21),
       (63, 'Access', TRUE, 22),
       (64, 'Trunk', TRUE, 22),
       (65, 'Voice', FALSE, 22),
       (66, 'Dynamic', FALSE, 22),
       (67, 'Hybrid', FALSE, 22),
       (68, 'Desirable', FALSE, 22),
       (69, 'As VoIP requires low latency and should therefore be treated a high priority from the switch.', TRUE, 23),
       (70, 'Calls don\'t function, when they aren\'t in a voice VLAN.', FALSE, 23),
       (71, 'True', TRUE, 24),
       (72, 'False', FALSE, 24),
       (73, 'They prevent broadcast storms.', TRUE, 25),
       (74, 'They allow for redundant networks by preventing loops.', TRUE, 25),
       (75, 'They allow for higher network bandwidth.', FALSE, 25),
       (76, 'They determine a root bridge which acts as the default gateway.', FALSE, 25),
       (77, 'SMB v1.0 (CIFS)', TRUE, 26),
       (78, 'SMB v2.0', TRUE, 26),
       (79, 'SMB v3.0', TRUE, 26),
       (80, 'SMB v4.0', FALSE, 26),
       (81, 'SMB v5.0', FALSE, 26),
       (82, 'SMB v6.0', FALSE, 26),
       (83,
        'With SMB version 3 Remote Direkt Memory Access (RDMA) was implemented offering high performance remote memory sharing, using low cpu utilisation.',
        TRUE, 27),
       (84, 'With SMB version 3 data encryption was added.', TRUE, 27),
       (85, 'With SMB version 3 data compression was added.', TRUE, 27),
       (86, 'With SMB version 3 VoIP was added.', FALSE, 27),
       (87, 'With SMB version 3 FTP was implemented.', FALSE, 27),
       (88, 'SMB is a protocol used for sharing various data (printer, files, etc.) on a LAN.', TRUE, 28),
       (89, 'SMB is used on Windows, while Samba is the open source implementation and support for Linux devices.',
        TRUE, 28),
       (90, 'SMB can use routing.', FALSE, 28),
       (91, 'SMB runs on FTP.', FALSE, 28),
       (92, 'samba', TRUE, 29),
       (93, 'samba-common', TRUE, 29),
       (94, 'samba-client', TRUE, 29),
       (95, 'smb', FALSE, 29),
       (96, 'samba-tools', FALSE, 29),
       (97, 'smb-extra', FALSE, 29),
       (98, '/etc/samba/smb.conf', TRUE, 30),
       (99, 'SMB support is a Windows feature that has to be activated.', TRUE, 31),
       (100, 'SMB packaged must first be downloaded with "winget", then activated using service "smb activate".', FALSE,
        31),
       (101, 'SMB must be activated using the command line.', FALSE, 31),
       (102, 'Every answer is true.', TRUE, 32),
       (103, 'DNAT', FALSE, 32),
       (104, 'SNAT', FALSE, 32),
       (105, 'PAT', FALSE, 32),
       (106, 'Because of IPv4 unique address depletion.', TRUE, 33),
       (107, 'Because of IPv6 creation.', FALSE, 33),
       (108, 'To solve end-to-end connectivity problems.', FALSE, 33),
       (109, 'ip nat inside/outside', TRUE, 34),
       (110, 'ip nat start', FALSE, 34),
       (111, 'systemctl start nat', FALSE, 34),
       (112, 'nat ip inside/outside', FALSE, 34),
       (113, 'show ip nat translations', TRUE, 35),
       (114, 'While SNAT uses the same inside global address, DNAT uses an address pool for requests.', TRUE, 36),
       (115, 'DNAT offers more flexibility for larger private networks, while SNAT is mostly used for static servers.',
        TRUE, 36),
       (116, 'SNAT allows much more hosts on the inside network.', FALSE, 36),
       (117, 'With DNAT, allocated/purchased public addresses aren\'t utilized efficiently.', FALSE, 36),
       (118, 'True', TRUE, 37),
       (119, 'False', FALSE, 37),
       (120, 'Any further request from inside will get dropped.', TRUE, 38),
       (121, 'The first leased address get\'s used again.', FALSE, 38),
       (122, 'The router switches to DPAT.', FALSE, 38),
       (123, 'Nothing.', FALSE, 38),
       (124, 'True', TRUE, 39),
       (125, 'False', FALSE, 39),
       (126, 'overload', TRUE, 40),
       (130, 'SSH', TRUE, 41),
       (131, 'FTP', FALSE, 41),
       (132, 'MD5 Hashing', FALSE, 41),
       (133, 'HTTPS', FALSE, 41),
       (134, 'Telnet', FALSE, 41),
       (135, 'Kerberos', FALSE, 41);
