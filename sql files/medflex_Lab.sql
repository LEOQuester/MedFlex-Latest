
CREATE TABLE Lab (
  Lab_ID int NOT NULL AUTO_INCREMENT,
  Lab_Name varchar(100) DEFAULT NULL,
  Location varchar(100) DEFAULT NULL,
  Contact_Num varchar(15) DEFAULT NULL,
  Email varchar(100) DEFAULT NULL,
  Username varchar(50) DEFAULT NULL,
  Password_hash varchar(255) DEFAULT NULL,
  PRIMARY KEY (Lab_ID)
) ;

INSERT INTO Lab (Lab_ID, Lab_Name, Location, Contact_Num, Email, Username, Password_hash) VALUES (1,'Medicare','grandpass','0724943352','induwaralakindu09@gmail.com','leo','$2y$10$JwnQMyioYOnOem.TiiRyHO5SqW3LkYBJZOaQl8gz6H/n9QbRBGzxa');


