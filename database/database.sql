PRAGMA foreign_keys=ON;

DROP TABLE IF EXISTS FAQ;
DROP TABLE IF EXISTS Comment;
DROP TABLE IF EXISTS Ticket;
DROP TABLE IF EXISTS Admin;
DROP TABLE IF EXISTS AgentDepartment;
DROP TABLE IF EXISTS Agent;
DROP TABLE IF EXISTS Department;
DROP TABLE IF EXISTS Hashtag;
DROP TABLE IF EXISTS Status;
DROP TABLE IF EXISTS Modification;
DROP TABLE IF EXISTS Client;

CREATE TABLE Client (
    userId INTEGER PRIMARY KEY AUTOINCREMENT,
    username NVAR(25) UNIQUE NOT NULL CHECK(LENGTH(username) >= 1),
    name NVARCHAR(120) NOT NULL CHECK(LENGTH(name) >= 1),
    email NVARCHAR(60) UNIQUE NOT NULL CHECK(LENGTH(email) >= 1),
    password NVARCHAR(40) NOT NULL
);

CREATE TABLE Agent (
    isAgent BOOLEAN NOT NULL,
    userId INTEGER PRIMARY KEY,
    FOREIGN KEY (userId) REFERENCES Client(userId) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE AgentDepartment (
    userId INTEGER,
    departmentId INTEGER,
    FOREIGN KEY (userId) REFERENCES Agent(userId) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (departmentID) REFERENCES Department(departmentID) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE (userId, departmentId)
);

CREATE TABLE Admin (
    isAdmin BOOLEAN NOT NULL,
    userId INTEGER PRIMARY KEY,
    FOREIGN KEY (userId) REFERENCES Agent(userId) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Ticket (
    ticketId INTEGER PRIMARY KEY,
    title TEXT NOT NULL CHECK(LENGTH(title) >= 10),
    body TEXT NOT NULL NULL CHECK(LENGTH(body) >= 10),
    department INTEGER,
    hashtags TEXT NOT NULL, /* Aqui vamos guardar um JSON com uma lista */
    priority INTEGER NOT NULL,
    status NVARCHAR(20) NOT NULL,
    date DATE NOT NULL,
    client INTEGER NOT NULL,
    agent INTEGER,

    FOREIGN KEY (status) REFERENCES Status(name) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (department) REFERENCES Department(departmentID) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (client) REFERENCES Client(userId) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (agent) REFERENCES Agent(userId) ON DELETE NO ACTION ON UPDATE CASCADE
);

CREATE TABLE FAQ (
    faqId INTEGER PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL
);

CREATE TABLE Comment (
  id INTEGER PRIMARY KEY,
  ticketId INTEGER,
  userId INTEGER NOT NULL,
  date DATE NOT NULL,
  text TEXT NOT NULL,
  faqId INTEGER,

  FOREIGN KEY (ticketID) REFERENCES Ticket(ticketID) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (userId) REFERENCES Client(userId) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (faqId) REFERENCES FAQ(faqId) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE Department (
    departmentId INTEGER PRIMARY KEY,
    name NVARCHAR(20) NOT NULL
);

CREATE TABLE Hashtag (
    name NVARCHAR(20) NOT NULL PRIMARY KEY
);

CREATE TABLE Status (
    name NVARCHAR(20) NOT NULL PRIMARY KEY
);

CREATE TABLE Modification (
    modificationID INTEGER PRIMARY KEY,
    field NVARCHAR(30) NOT NULL,
    old NVARCHAR(60) NOT NULL,
    new NVARCHAR(60) NOT NULL,
    date DATE NOT NULL,
    ticketID INTEGER NOT NULL,
    userId INTEGER NOT NULL,
    FOREIGN KEY (ticketID) REFERENCES Ticket(ticketID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (userId) REFERENCES Client(userId) ON DELETE CASCADE ON UPDATE CASCADE
);


INSERT INTO Client (name, username, email, password) VALUES
('Pedro Madureira', 'ram', 'pedro@gmail.com', '$2y$10$s.G6lPMGoZcvm3G7LApaf.9LORZhyIXc0dZWPjj4kS9dEkRQRwcGW'), --passwordsecreta
('Tomas Gaspar', 'gaspar', 'tomasgaspar@gmail.com', '$2y$10$Uv/ibRHkpx1s4oo1Z3266O092xnAtGclVXFnNghcqo7J2COHUOfm2'), --1234567
('Daniel Gago', 'gago', 'danielgago@gmail.com', '$2y$10$QTMpiMyuRWG.FGT0j33hIu5CBBv8E72N718xWEhAwrZBn/pbwQr1K'), --daniel_faro123
('Maria Silva', 'mariasilva', 'maria.silva@gmail.com', '$2y$10$O1J6taI2fjlRi8VKLdfr2.2HloYtj.foai7slI6oG9mZ1K3axu/i6'),
('João Santos', 'joaosantos', 'joao.santos@gmail.com', '$2y$10$NtiTDjPtvO9W9xiH.5RcI.BNOj64RM2HEALCmr5c8E/fq/7Olhlq6'),
('Ana Oliveira', 'anaoliveira', 'ana.oliveira@gmail.com', '$2y$10$lV9uM11swfuC8D9dC4yta.KqKbjOCE7OuIsW6/Rmcpe6WfOpIUDme'), 
('Carlos Costa', 'carloscosta', 'carlos.costa@gmail.com', '$2y$10$FhYr1ZlJ.xYHd.nDWmcseO77M5Q.fZLludYeLKgEpQG1fZcPhFOmC'),
('Sofia Rodrigues', 'sofiarodrigues', 'sofia.rodrigues@gmail.com', '$2y$10$fr0eGyCZcV1VvX2hv0SYYenLdcGZlLYDvif7Fgh8rHLtO9o2s8AFK'), 
('Ricardo Fernandes', 'ricardofernandes', 'ricardo.fernandes@gmail.com', '$2y$10$6gMXJ6XC0HNtO6Dd/9gy0.x1GDA5fNzk.wHnqFeCNJCUflOeT9erW'), 
('Marta Pereira', 'martapereira', 'marta.pereira@gmail.com', '$2y$10$ZLjD1zpgZzNV1g/0Y9hH1OfOgoEktYPc.pZVJUUDRb6F6Tp2CYVYm'), 
('Hugo Almeida', 'hugoalmeida', 'hugo.almeida@gmail.com', '$2y$10$CpEFiF9t5sQs47qB0V5lmebDltoft48UAKExNRYST9j1iq/kpAPhS'), 
('Luisa Santos', 'luisasantos', 'luisa.santos@gmail.com', '$2y$10$18ohqQ4GbjtI6r4hi2JNlObt5aH2ZXUm/3/4WIECjzDe7zXUFRJKy'), 
('Manuel Sousa', 'manuelsousa', 'manuel.sousa@gmail.com', '$2y$10$F6w6a8tZ3CftilVzk8NfOOxq9zHkrrYO2ZgQ6QuLkL79w32wz3xdy'),
('Patricia Torres', 'patorres', 'patricia.torres@gmail.com', '$2y$10$8W.JEYNSJ9mSJkHRKFuNZu4n6F6Yva5bKrhf2YSNJpGnp.dF0kxw2'),
('Raul Oliveira', 'rauloliveira', 'raul.oliveira@gmail.com', '$2y$10$UKC1nZQzJK9nRH9sYXlnGuzpK1n/NGnn0qT7S/5Oe8WZbZC71ujpm'), 
('Carolina Ferreira', 'carolinaferreira', 'carolina.ferreira@gmail.com', '$2y$10$4zXNpNpgPVbySyO5EsD0ReHzwSGczJLHLXY1qgmFtrmp8Z4ZiW8/a'),
('Miguel Gonçalves', 'miguelgoncalves', 'miguel.goncalves@gmail.com', '$2y$10$zByDMZMXlWujBQd/BqL1KOZEBz8Z2Cq8.m1aLvTy2F9gyKm58Dd6G'), 
('Inês Costa', 'inescosta', 'ines.costa@gmail.com', '$2y$10$Koi6bX9X.bR/qw6kbiYU2.uAmsA.eKnrRJFGmst1meY1ESOKqV4I6'),
('Eduardo Santos', 'eduardosantos', 'eduardo.santos@gmail.com', '$2y$10$GYA7qmny5CZmJ/FahWxVBevXw07lTD4Qk0BgOhlQ5JtOXGgsI4DRa'),
('Helena Rodrigues', 'helenarodrigues', 'helena.rodrigues@gmail.com', '$2y$10$/8Jfc2bSvWllN5x6tlzfu.MzgA7yW3OWj5hz/pXX5HHqqbkNc4DBW'),
('Bruno Costa', 'brunocosta', 'bruno.costa@gmail.com', '$2y$10$7mRdcjBX1U9Civ8s0qkczu.mTDuXWs6rPXH7pFXChTdbif65Y3hI6'),
('Andreia Martins', 'andreiamartins', 'andreia.martins@gmail.com', '$2y$10$Spa5AnBeUhx4B.gCER9bE.uXhZo2Eug2IbQk67BqceTWRlsIzkK6u'),
('Fernando Silva', 'fernandosilva', 'fernando.silva@gmail.com', '$2y$10$YV1mOLYQ.7kK1dSpBw5dF.Qs3dF1bR.g9j9PL2KIf3R/L5cBe09eS');

INSERT INTO Agent (isAgent, userId) VALUES 
(true, 1),
(true, 2),
(true, 3),
(true, 4),
(true, 5),
(true, 6),
(true, 7),
(false, 8),
(false, 9),
(false, 10),
(false, 11),
(false, 12),
(false, 13),
(false, 14),
(false, 15),
(false, 16),
(false, 17),
(false, 18),
(false, 19),
(false, 20),
(false, 21),
(false, 22),
(false, 23);


INSERT INTO Admin (isAdmin, userId) VALUES
(true, 1),
(true, 2),
(true, 3),
(false, 4),
(false, 5),
(false, 6),
(false, 7),
(false, 8),
(false, 9),
(false, 10),
(false, 11),
(false, 12),
(false, 13),
(false, 14),
(false, 15),
(false, 16),
(false, 17),
(false, 18),
(false, 19),
(false, 20),
(false, 21),
(false, 22),
(false, 23);

INSERT INTO Department (departmentId, name) VALUES
(1, 'Human Resources'),
(2, 'Information Technology'),
(3, 'Sales'),
(4, 'Finance'),
(5, 'Customer Support'),
(6, 'Marketing');

INSERT INTO Status (name) VALUES ('Open');
INSERT INTO Status (name) VALUES ('Assigned');
INSERT INTO Status (name) VALUES ('Closed');

INSERT INTO Hashtag (name) VALUES ('login issues');
INSERT INTO Hashtag (name) VALUES('payment problems');
INSERT INTO Hashtag (name) VALUES('bug report');
INSERT INTO Hashtag (name) VALUES('feature request');
INSERT INTO Hashtag (name) VALUES('account help');
INSERT INTO Hashtag (name) VALUES('billing inquiry');
INSERT INTO Hashtag (name) VALUES('technical support');
INSERT INTO Hashtag (name) VALUES('installation issues');
INSERT INTO Hashtag (name) VALUES('performance problems');
INSERT INTO Hashtag (name) VALUES('product feedback');
INSERT INTO Hashtag (name) VALUES('general inquiry');
INSERT INTO Hashtag (name) VALUES('cancellation request');
INSERT INTO Hashtag (name) VALUES('upgrades and downgrades');
INSERT INTO Hashtag (name) VALUES('website navigation issues');
INSERT INTO Hashtag (name) VALUES('mobile app issues');
INSERT INTO Hashtag (name) VALUES('product usage questions');
INSERT INTO Hashtag (name) VALUES('account security');
INSERT INTO Hashtag (name) VALUES('data privacy concerns');
INSERT INTO Hashtag (name) VALUES('server downtime');
INSERT INTO Hashtag (name) VALUES('product integration issues');
INSERT INTO Hashtag (name) VALUES('user interface feedback');

INSERT INTO AgentDepartment (userId, departmentID) VALUES (1, 1);
INSERT INTO AgentDepartment (userId, departmentID) VALUES (1, 2);
INSERT INTO AgentDepartment (userId, departmentID) VALUES (3, 3);
INSERT INTO AgentDepartment (userId, departmentID) VALUES (4, 4);
INSERT INTO AgentDepartment (userId, departmentID) VALUES (4, 1);
INSERT INTO AgentDepartment (userId, departmentID) VALUES (7, 5);
INSERT INTO AgentDepartment (userId, departmentID) VALUES (7, 6);
INSERT INTO AgentDepartment (userId, departmentID) VALUES (7, 2);

INSERT INTO AgentDepartment (userId, departmentID) VALUES (2, 1);
INSERT INTO AgentDepartment (userId, departmentID) VALUES (5, 2);
INSERT INTO AgentDepartment (userId, departmentID) VALUES (6, 3);
INSERT INTO AgentDepartment (userId, departmentID) VALUES (5, 5);
INSERT INTO AgentDepartment (userId, departmentID) VALUES (3, 6);


INSERT INTO Ticket (ticketId, title, body, department, hashtags, priority, status, date, client, agent)
VALUES
(1, 'Installation Issues', 'I am experiencing difficulties while installing the software. Can someone provide guidance?', NULL, '["installation issues"]', 2, 'Open', '2023-01-20', 9, NULL),
(2, 'Website Navigation Issues', 'I am having trouble navigating through the website. Can someone assist me?', NULL, '["website navigation issues"]', 2, 'Open', '2023-02-11', 11, NULL),
(3, 'Payment Problems', 'I am unable to make a payment for my subscription. Please help!', NULL, '["payment problems"]', 1, 'Open', '2023-02-13', 4, NULL),
(4, 'Product Feedback', 'I have some valuable feedback regarding the product. Who can I talk to?', NULL, '["product feedback"]', 3, 'Open', '2023-03-02', 7, NULL),
(5, 'Technical Support Needed', 'I require technical assistance for a software issue. Please assist!', 2, '["technical support","bug report"]', 2, 'Assigned', '2023-03-15', 5, 1),
(6, 'Performance Problems', 'The software is running very slow and laggy. It is impacting my work productivity.', 4, '["performance problems"]', 2, 'Assigned', '2023-03-19', 6, 4),
(7, 'Login Problems', 'I am unable to log in to my account. Every time I try, it gives me an error message.', 1, '["login issues","technical support"]', 1, 'Assigned', '2023-04-04', 7, 4),
(8, 'Account Help Needed', 'I need assistance with managing my account settings. How can I update my email address?', 5, '["account help"]', 2, 'Assigned', '2023-04-10', 8, 7),
(9, 'Bug Report', 'I found a bug in the latest software release. Certain features are not working as expected.', 5, '["bug report"]', 3, 'Open', '2023-04-20', 11, NULL),
(10, 'Feature Request', 'I have a suggestion for a new feature that would greatly enhance the user experience.', 6, '["feature request"]', 2, 'Assigned', '2023-04-21', 9, 7),
(11, 'General Inquiry', 'I have a general question about the product. Can someone provide more information?', 5, '["general inquiry"]', 1, 'Open', '2023-04-22', 10, NULL),
(12, 'Login Problems', 'I am unable to log in to my account. Every time I try, it gives me an error message.', 1, '["login issues","technical support"]', 1, 'Assigned', '2023-04-29', 11, 4),
(13, 'Payment Problems', 'I am having issues with making a payment for my subscription. Can you assist me?', 4, '["payment problems","billing inquiry"]', 2, 'Assigned', '2023-05-01', 12, 4),
(14, 'Bug Report', 'I found a bug in the latest software update. Certain features are not functioning properly.', 3, '["bug report","technical support"]', 3, 'Assigned', '2023-05-02', 9, 3),
(15, 'Feature Request', 'I have a suggestion for a new feature that would greatly improve the product.', 5, '["feature request","product feedback"]', 2, 'Assigned', '2023-05-20', 11, 7),
(16, 'Account Help Needed', 'I need assistance with managing my account settings. How can I change my password?', 5, '["account help","account security"]', 2, 'Assigned', '2023-05-14', 9, 7),
(17, 'Website Navigation Issues', 'I am encountering difficulties while navigating through the website. Can someone guide me?', 2, '["website navigation issues","user interface feedback"]', 2, 'Assigned', '2023-05-15', 11, 1),
(18, 'Mobile App Issues', 'The mobile app keeps crashing whenever I try to use it. Please help!', 2, '["mobile app issues","technical support"]', 2, 'Assigned', '2023-05-18', 1, 1),
(19, 'Product Usage Questions', 'I have some questions about how to use certain features of the product. Can you provide assistance?', 5, '["product usage questions","general inquiry"]', 1, 'Open', '2023-05-19', 10, NULL),
(20, 'Performance Problems', 'The software performance has been very slow lately. It is affecting my work. Please resolve this issue.', 4, '["performance problems","server downtime"]', 2, 'Assigned', '2023-05-18', 6, 4),
(21, 'Product Feedback', 'I have some feedback regarding the product. How can I share it with the development team?', 3, '["product feedback","user interface feedback"]', 3, 'Assigned', '2023-05-20', 8, 3);


INSERT INTO Modification (field, old, new, date, ticketID, userId) VALUES 
('Agent', '', '7', '2023-05-20', 15, 7),
('Agent', '', '3', '2023-05-20', 21, 3),
('Agent', '', '1', '2023-05-18', 18, 1),
('Agent', '', '4', '2023-05-18', 20, 4),
('Agent', '', '1', '2023-05-15', 17, 1),
('Agent', '', '7', '2023-05-14', 16, 7),
('Agent', '', '3', '2023-05-02', 14, 3),
('Agent', '', '4', '2023-05-01', 13, 4),
('Agent', '', '4', '2023-04-29', 12, 4),
('Agent', '', '7', '2023-04-21', 10, 7),
('Agent', '', '7', '2023-04-10', 8, 7),
('Agent', '', '4', '2023-04-10', 7, 4),
('Agent', '', '4', '2023-03-19', 6, 4),
('Agent', '', '1', '2023-03-15', 5, 1);

INSERT INTO Comment (id, ticketID, userId, date, text)
VALUES (1, 1, 9, '2023-01-20', 'I had forgotten to download the recent version. It does not work with the older one');


INSERT INTO FAQ (faqId, question, answer)
VALUES (1, 'How do I submit a new ticket?', 'To submit a new ticket, click on the "Submit" button on the new ticket form. Fill in the required details such as ticket subject, priority and description. Then, click on the "Submit" button to create the ticket.');

INSERT INTO FAQ (faqId, question, answer)
VALUES (2, 'Can I track the progress of my ticket?', 'Yes, you can track the progress of your ticket by clicking on your ticket. There, you will find the status, assigned agent, and any updates related to your ticket.');

INSERT INTO FAQ (faqId, question, answer)
VALUES (3, 'How long does it take to receive a response to my ticket?', 'The response time for tickets may vary depending on the complexity and priority of the issue. Our support team aims to respond within 24-48 hours, excluding weekends and holidays.');

INSERT INTO FAQ (faqId, question, answer)
VALUES (4, 'How can I view my ticket history?', 'To view your ticket history, navigate to the your ticket. There, you will find a log will all the changes.');

INSERT INTO FAQ (faqId, question, answer)
VALUES (5, 'Can I reopen a closed ticket?', 'No, you cannot reopen a closed ticket. If you need further assistance or if the issue reoccurs, create a new ticket.');

INSERT INTO FAQ (faqId, question, answer)
VALUES (6, 'How do I update my contact information?', 'To update your contact information, go to the "Profile" section. There, you can modify your email address, phone number, or any other relevant details.');

INSERT INTO FAQ (faqId, question, answer)
VALUES (7, 'Can I assign a ticket to a specific team or department?', 'Yes, during ticket creation, you can assign the ticket to a specific team or department by selecting the appropriate option from the category dropdown. The ticket will then be routed accordingly.');
