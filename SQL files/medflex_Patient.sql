
CREATE TABLE Patient (
  Patient_ID int NOT NULL AUTO_INCREMENT,
  F_name varchar(50) DEFAULT NULL,
  L_name varchar(50) DEFAULT NULL,
  DOB date DEFAULT NULL,
  Gender varchar(10) DEFAULT NULL,
  Address varchar(255) DEFAULT NULL,
  Email varchar(100) DEFAULT NULL,
  Username varchar(50) DEFAULT NULL,
  Password_hash varchar(255) DEFAULT NULL,
  PRIMARY KEY (Patient_ID)
) ;


INSERT INTO Patient (Patient_ID, F_name, L_name, DOB, Gender, Address, Email, Username, Password_hash) VALUES (1,'Kavindu','Perera','1998-04-15','Male','45 Temple Road, Colombo','kavindu.perera@gmail.com','kavindup','Kp@12345'),(2,'Nimesha','Fernando','2002-09-21','Female','12 Park Street, Galle','nimesha.fernando@yahoo.com','nimeshaf','Nf@78901'),(3,'Tharindu','Wijesinghe','1995-12-10','Male','77 Lake View, Kandy','tharindu.wije@gmail.com','tharinduw','Tw@45678'),(4,'Sajini','Silva','2001-03-03','Female','34 Beach Road, Negombo','sajini.silva@hotmail.com','sajinis','Ss@11223'),(5,'Dinuka','Bandara','1999-07-27','Male','89 Hill Street, Matara','dinuka.bandara@gmail.com','dinukab','Db@99887'),(6,'Ishara','Gunasekara','2003-05-19','Female','23 Green Lane, Kurunegala','ishara.gunasekara@gmail.com','isharag','Ig@55443'),(7,'Kasun','Jayawardena','1997-11-08','Male','56 Rose Avenue, Colombo 05','kasun.jay@gmail.com','kasunj','Kj@66778'),(8,'Ruwani','De Silva','2000-02-25','Female','10 Palm Grove, Panadura','ruwani.desilva@gmail.com','ruwanid','Rd@44332'),(9,'Chathura','Abeysinghe','1996-08-30','Male','78 Lotus Road, Nugegoda','chathura.abey@gmail.com','chathuraa','Ca@12121'),(10,'Harini','Ratnayake','2004-01-12','Female','19 Flower Street, Kegalle','harini.ratna@gmail.com','harinir','Hr@90909'),(11,'Saman','Kumara','1999-02-23','Male','No.20, Galle Road, Dehiwala','samanK@gmail.com','Saman K','$2y$10$gY4PQmeEoFhaDDbrLtahF./TbkJzsar3115r7fG6VA3RSQcZfCuiu');

