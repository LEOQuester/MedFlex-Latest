
CREATE TABLE Lab_Patient (
  Lab_ID int NOT NULL,
  Patient_ID int NOT NULL,
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (Lab_ID,Patient_ID),
  KEY Patient_ID (Patient_ID),
  CONSTRAINT Lab_Patient_ibfk_1 FOREIGN KEY (Lab_ID) REFERENCES Lab (Lab_ID) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT Lab_Patient_ibfk_2 FOREIGN KEY (Patient_ID) REFERENCES Patient (Patient_ID) ON DELETE CASCADE ON UPDATE CASCADE
) ;


INSERT INTO Lab_Patient (Lab_ID, Patient_ID, created_at) VALUES (1,11,'2025-10-09 11:03:17');

