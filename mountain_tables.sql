BEGIN
      EXECUTE IMMEDIATE 'DROP TABLE Rental';
EXCEPTION
      WHEN OTHERS THEN NULL;
END;
/
BEGIN
      EXECUTE IMMEDIATE 'DROP TABLE Guest';
EXCEPTION
      WHEN OTHERS THEN NULL;
END;
/
BEGIN
      EXECUTE IMMEDIATE 'DROP TABLE Equipment';
EXCEPTION
      WHEN OTHERS THEN NULL;
END;
/
BEGIN
      EXECUTE IMMEDIATE 'DROP TABLE Employee';
EXCEPTION
      WHEN OTHERS THEN NULL;
END;
/
BEGIN
      EXECUTE IMMEDIATE 'DROP TABLE Tester';
EXCEPTION
      WHEN OTHERS THEN NULL;
END;
/


CREATE TABLE Guest
	(guest_name VARCHAR(50),
	phone_number VARCHAR(20),
	email VARCHAR(50) UNIQUE,
	gender VARCHAR(20),
	CONSTRAINT pk_guest PRIMARY KEY (guest_name, phone_number));

CREATE TABLE Equipment
	(serial_num VARCHAR(25),
	equipment_size VARCHAR(10),
	equipment_type VARCHAR(20), 
	cost DECIMAL(8, 2),
	brand VARCHAR(20),
	CONSTRAINT pk_equipment PRIMARY KEY (serial_num));

CREATE TABLE Employee
	(SIN INT,
	employee_name VARCHAR(50),
	employed_since DATE,
	CONSTRAINT pk_employee PRIMARY KEY(SIN));

CREATE TABLE Rental
	(guest_name VARCHAR(50), 
	phone_number VARCHAR(20), 
	serial_num VARCHAR(25), 
	rental_date DATE, 
	SIN INT,
	CONSTRAINT pk_rental PRIMARY KEY(guest_name, phone_number, serial_num, rental_date), 
	CONSTRAINT fk_guest FOREIGN KEY (guest_name, phone_number) REFERENCES Guest (guest_name, phone_number)
		ON DELETE CASCADE, 
	CONSTRAINT fk_equipment FOREIGN KEY (serial_num) REFERENCES Equipment (serial_num)
		ON DELETE CASCADE,
	CONSTRAINT fk_employee FOREIGN KEY (SIN) REFERENCES Employee (SIN));

-- Guest
INSERT INTO Guest VALUES ('A', '+1-121', 'A@site1.com', 'M');
INSERT INTO Guest VALUES ('A', '+1-122', 'A@site2.com', 'M');
INSERT INTO Guest VALUES ('B', '1', 'B@site1.com', 'M');
INSERT INTO Guest VALUES ('C', '2', 'C@site2.com', 'F');
INSERT INTO Guest VALUES ('Aa', '+33-333', 'Aa@site1.com', 'F');
INSERT INTO Guest VALUES ('Bb', '+33-334', 'Bb@site2.com', 'Other');

-- Equipment
-- helmets
INSERT INTO Equipment VALUES ('121', 'XXL', 'helmet', 4.99, 'K2');
INSERT INTO Equipment VALUES ('122', 'S', 'helmet', 7.00, 'The North Face');
INSERT INTO Equipment VALUES ('123', 'S', 'helmet', 7.00, 'Sorel');
INSERT INTO Equipment VALUES ('124', 'M', 'helmet', 9.00, 'The North Face');
INSERT INTO Equipment VALUES ('125', 'M', 'helmet', 8.00, 'K2');
-- goggles
INSERT INTO Equipment VALUES ('1A', 'L', 'goggles', 5.14, 'K2');
INSERT INTO Equipment VALUES ('2A', 'M', 'goggles', 15.05, 'Polaris');
INSERT INTO Equipment VALUES ('1B', 'M', 'goggles', 15.05, 'Polaris');
INSERT INTO Equipment VALUES ('2B', 'XXL', 'goggles', 1.00, 'The North Face');
INSERT INTO Equipment VALUES ('1C', 'L', 'goggles', 8.00, 'K2');
-- gloves
INSERT INTO Equipment VALUES ('2C', 'S', 'gloves', 7.00, 'The North Face');
INSERT INTO Equipment VALUES ('126', 'XXL', 'gloves', 7.00, 'K2');
INSERT INTO Equipment VALUES ('127', 'XXS', 'gloves', 50.00, 'K2');
INSERT INTO Equipment VALUES ('128', 'XXL', 'gloves', 1.00, 'Polaris');
INSERT INTO Equipment VALUES ('129', 'L', 'gloves', 8.00, 'K2');
INSERT INTO Equipment VALUES ('1D', 'XXS', 'gloves', 50.00, 'K2');
INSERT INTO Equipment VALUES ('2D', 'XXL', 'gloves', 7.00, 'K2');
-- boots
INSERT INTO Equipment VALUES ('E1', '10F', 'boots', 10.00, 'Sorel');
INSERT INTO Equipment VALUES ('E2', '8M', 'boots', 10.00, 'Sorel');
INSERT INTO Equipment VALUES ('F1', '8.5M', 'boots', 7.00, 'K2');
INSERT INTO Equipment VALUES ('F2', '5M', 'boots', 10.00, 'Sorel');
INSERT INTO Equipment VALUES ('G1', '6F', 'boots', 7.00, 'K2');
INSERT INTO Equipment VALUES ('G2', '6F', 'boots', 7.00, 'K2');
INSERT INTO Equipment VALUES ('G3', '6F', 'boots', 7.00, 'K2');

-- Employee
INSERT  INTO Employee VALUES (111111111, 'D', TO_DATE('2010-01-01','YYYY-MM-DD'));
INSERT  INTO Employee VALUES (111111112, 'D', TO_DATE('2010-07-01','YYYY-MM-DD'));
INSERT  INTO Employee VALUES (111111113, 'Dd', TO_DATE('2011-02-02','YYYY-MM-DD'));
INSERT  INTO Employee VALUES (111111114, 'E', TO_DATE('2011-08-02','YYYY-MM-DD'));
INSERT  INTO Employee VALUES (111111115, 'Ee', TO_DATE('2013-01-01','YYYY-MM-DD'));

-- A +1-121 Rentals
INSERT INTO Rental VALUES ('A', '+1-121', '121', TO_DATE('2010-12-01','YYYY-MM-DD'), 111111111);
INSERT INTO Rental VALUES ('A', '+1-121', '121', TO_DATE('2011-01-01','YYYY-MM-DD'), 111111112);
INSERT INTO Rental VALUES ('A', '+1-121', '122', TO_DATE('2011-01-01','YYYY-MM-DD'), 111111112);
INSERT INTO Rental VALUES ('A', '+1-121', '123', TO_DATE('2011-01-01','YYYY-MM-DD'), 111111111);
INSERT INTO Rental VALUES ('A', '+1-121', '122', TO_DATE('2011-05-01','YYYY-MM-DD'), 111111112);
-- A +1-122 Rentals
INSERT INTO Rental VALUES ('A', '+1-122', '121', TO_DATE('2010-12-01','YYYY-MM-DD'), 111111111);
INSERT INTO Rental VALUES ('A', '+1-122', '122', TO_DATE('2010-12-01','YYYY-MM-DD'), 111111111);
INSERT INTO Rental VALUES ('A', '+1-122', '1A', TO_DATE('2011-01-01','YYYY-MM-DD'), 111111112);
INSERT INTO Rental VALUES ('A', '+1-122', '2A', TO_DATE('2011-01-01','YYYY-MM-DD'), 111111111);
INSERT INTO Rental VALUES ('A', '+1-122', 'E1', TO_DATE('2011-05-01','YYYY-MM-DD'), 111111113);
INSERT INTO Rental VALUES ('A', '+1-122', 'E1', TO_DATE('2012-12-01','YYYY-MM-DD'), 111111114);
INSERT INTO Rental VALUES ('A', '+1-122', 'E1', TO_DATE('2012-12-02','YYYY-MM-DD'), 111111115);
-- B 1 Rentals
INSERT INTO Rental VALUES ('B', '1', '124', TO_DATE('2013-12-01','YYYY-MM-DD'), 111111115); 
INSERT INTO Rental VALUES ('B', '1', '124', TO_DATE('2013-12-02','YYYY-MM-DD'), 111111115);
