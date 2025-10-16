
CREATE TABLE Prediction_History (
  Prediction_ID int NOT NULL AUTO_INCREMENT,
  Report_ID int NOT NULL,
  Ferritin decimal(7,2) DEFAULT NULL,
  Vitamin_B12 decimal(6,2) DEFAULT NULL,
  CRP decimal(5,2) DEFAULT NULL,
  Afp decimal(6,2) DEFAULT NULL,
  HbA1c decimal(4,2) DEFAULT NULL,
  Cystatin_C decimal(4,2) DEFAULT NULL,
  PRIMARY KEY (Prediction_ID),
  FOEIGN KEY (Report_ID) REFERENCES Report(Report_ID)
) ;

INSERT INTO Prediction_History (Prediction_ID, Report_ID, Ferritin, Vitamin_B12, CRP, Afp, HbA1c, Cystatin_C) VALUES (1,13,8.92,204.67,3.93,3.72,5.29,0.61),(2,14,8.92,204.67,3.93,3.72,5.29,0.61);

