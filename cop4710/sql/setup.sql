drop DATABASE IF EXISTS college_events;

create DATABASE college_events;

use college_events;

CREATE TABLE locations (
	locationsId INT(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
	locationsLong VARCHAR(128) NOT NULL,
	locationsLat VARCHAR(128) NOT NULL,
	UNIQUE(locationsLong,locationsLat)
)ENGINE = InnoDB;

-- Handle university creation at sign-up
CREATE TABLE universities (
	universitiesId INT(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
	universitiesName VARCHAR(30) NOT NULL,
	universitiesNumStudents INT(11) NOT NULL DEFAULT 0,
	universitiesDesc VARCHAR(255),
	UNIQUE(universitiesName)
)ENGINE = InnoDB;

CREATE TABLE universitylocation (
	universitylocationUid INT(11) NOT NULL,
	universitylocationLid INT(11) NOT NULL,
	PRIMARY KEY(universitylocationUid, universitylocationLid),
	CONSTRAINT universitylocationuid_fk
	FOREIGN KEY(universitylocationUid) REFERENCES universities(universitiesId) ON DELETE CASCADE,
	CONSTRAINT universitylocationlid_fk
	FOREIGN KEY(universitylocationLid) REFERENCES locations(locationsId) ON DELETE CASCADE
)ENGINE = InnoDB;

CREATE TABLE users (
    usersId INT(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    usersName VARCHAR(128) NOT NULL,
    usersPwd VARCHAR(128) NOT NULL
)ENGINE = InnoDB;

CREATE TABLE universityuser (
	universityuserUserid INT(11) NOT NULL,
	universityuserUniid INT(11) NOT NULL,
	PRIMARY KEY(universityuserUserid, universityuserUniid),
	CONSTRAINT universityuseruserid_fk
	FOREIGN KEY(universityuserUserid) REFERENCES users(usersId) ON DELETE CASCADE,
	CONSTRAINT universityuseruniid_fk
	FOREIGN KEY(universityuserUniid) REFERENCES universities(universitiesId) ON DELETE CASCADE
)ENGINE = InnoDB;

CREATE TABLE superadmins (
    superadminsId INT(11) NOT NULL,
    PRIMARY KEY(superadminsId),
	CONSTRAINT superadminssid_fk
	FOREIGN KEY(superadminsId) REFERENCES users(usersId) ON DELETE CASCADE
)ENGINE = InnoDB;

CREATE TABLE admins (
    adminsId INT(11) NOT NULL,
    PRIMARY KEY(adminsId),
	CONSTRAINT adminsaid_fk
	FOREIGN KEY(adminsId) REFERENCES users(usersId) ON DELETE CASCADE
)ENGINE = InnoDB;

-- RSO names are stored as 'uni: rsoname'
CREATE TABLE rsos (
	rsosId INT(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
	rsosName VARCHAR(50) NOT NULL,
	rsosDesc VARCHAR(255),
	rsosOwnerId INT(11) NOT NULL,
	rsosStatus ENUM("active","inactive") DEFAULT "inactive",
	CONSTRAINT rsosoid_fk
	FOREIGN KEY(rsosOwnerId) REFERENCES users(usersId) ON DELETE CASCADE,
	UNIQUE(rsosName)
)ENGINE = InnoDB;

CREATE TABLE rsouniversity (
	rsouniversityUid INT(11),
	rsouniversityRid INT(11) NOT NULL,
	PRIMARY KEY(rsouniversityUid,rsouniversityRid),
	CONSTRAINT rsouniversityuid_fk
	FOREIGN KEY(rsouniversityUid) REFERENCES universities(universitiesId) ON DELETE CASCADE,
	CONSTRAINT rsouniversityrid_fk
	FOREIGN KEY(rsouniversityRid) REFERENCES rsos(rsosId) ON DELETE CASCADE
)ENGINE = InnoDB;

CREATE TABLE rsouser (
	rsouserUid INT(11) NOT NULL,
	rsouserRid INT(11) NOT NULL,
	PRIMARY KEY(rsouserUid,rsouserRid),
	CONSTRAINT rsouseruid_fk
	FOREIGN KEY(rsouserUid) REFERENCES users(usersId) ON DELETE CASCADE,
	CONSTRAINT rsouserrid_fk
	FOREIGN KEY(rsouserRid) REFERENCES rsos(rsosId) ON DELETE CASCADE
)ENGINE = InnoDB;

CREATE TABLE events (
	eventsId INT(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
	eventsLid INT(11) NOT NULL,
	eventsName VARCHAR(50) NOT NULL,
	eventsDesc VARCHAR(255) NOT NULL,
	eventsDateTime DATETIME NOT NULL,
	eventsPhone VARCHAR(12) NOT NULL,
	eventsUid INT(11) NOT NULL,
	eventsRid INT(11),
	CONSTRAINT eventsuid_fk
	FOREIGN KEY(eventsUid) REFERENCES universities(universitiesId) ON DELETE CASCADE,
	CONSTRAINT eventslid_fk
	FOREIGN KEY(eventsLid) REFERENCES locations(locationsId) ON DELETE CASCADE,
	CONSTRAINT eventsrid_fk
	FOREIGN KEY(eventsRid) REFERENCES rsos(rsosId) ON DELETE CASCADE,
	UNIQUE(eventsLid,eventsDateTime)
)ENGINE = InnoDB;

CREATE TABLE eventuser (
	eventuserUid INT(11) NOT NULL,
	eventuserEid INT(11) NOT NULL,
	PRIMARY KEY(eventuserUid,eventuserEid),
	CONSTRAINT eventuseruid_fk
	FOREIGN KEY(eventuserUid) REFERENCES users(usersId) ON DELETE CASCADE,
	CONSTRAINT eventusereid_fk
	FOREIGN KEY(eventuserEid) REFERENCES events(eventsId) ON DELETE CASCADE
);

CREATE TABLE privateevents (
	privateeventsId INT(11) NOT NULL,
	PRIMARY KEY(privateeventsId),
	CONSTRAINT privateeventsid_fk
	FOREIGN KEY(privateeventsId) REFERENCES events(eventsId) ON DELETE CASCADE
);

CREATE TABLE publicevents (
	publiceventsId INT(11) NOT NULL,
	PRIMARY KEY(publiceventsId),
	CONSTRAINT publiceventsid_fk
	FOREIGN KEY(publiceventsId) REFERENCES events(eventsId) ON DELETE CASCADE
);

CREATE TABLE rsoevents (
	rsoeventsId INT(11) NOT NULL,
	PRIMARY KEY(rsoeventsId),
	CONSTRAINT rsoeventsid_fk
	FOREIGN KEY(rsoeventsId) REFERENCES events(eventsId) ON DELETE CASCADE
);

-- insertion and deletion done on php end
CREATE TABLE eventapproval (
	eventapprovalEid INT(11) NOT NULL,
	eventapprovalSid INT(11) NOT NULL,
	PRIMARY KEY(eventapprovalEid,eventapprovalSid),
	CONSTRAINT eventapprovaleid_fk
	FOREIGN KEY(eventapprovalEid) REFERENCES events(eventsId) ON DELETE CASCADE,
	CONSTRAINT eventapprovalsid_fk
	FOREIGN KEY(eventapprovalSid) REFERENCES superadmins(superadminsId) ON DELETE CASCADE
)ENGINE = InnoDB;

CREATE TABLE comments (
	commentsEid INT(11) NOT NULL,
	commentsUid INT(11) NOT NULL,
	commentsTime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	commentsDesc VARCHAR(255) NOT NULL,
	PRIMARY KEY(commentsEid, commentsUid, commentsTime),
	CONSTRAINT commentseid_fk
	FOREIGN KEY(commentsEid) REFERENCES events(eventsId) ON DELETE CASCADE,
	CONSTRAINT commentsuid_fk
	FOREIGN KEY(commentsUid) REFERENCES users(usersId) ON DELETE CASCADE
)ENGINE = InnoDB;


DELIMITER $$
-- trigger setting rso to active if 5 or more people are in the group. adds admin privledges to owner of the rso (regardless of activity)
CREATE TRIGGER RSOStatusUpdateA AFTER INSERT ON rsouser
FOR EACH ROW BEGIN
IF ((SELECT COUNT(*) FROM rsouser M WHERE M.rsouserRid = NEW.rsouserRid) > 4)
THEN
	UPDATE rsos SET rsosStatus = "active" WHERE rsosId = NEW.rsouserRid;
END IF;
-- Adds admin if another one doesnt already exist for rso that is added into and user isnt already an admin
IF (((SELECT COUNT(*) FROM rsos R WHERE R.rsosId = NEW.rsouserRid AND R.rsosOwnerId = NEW.rsouserUid) > 0)
AND ((SELECT COUNT(*) FROM admins A WHERE A.adminsId = NEW.rsouserUid) < 1))
THEN
	INSERT INTO admins(adminsId) VALUES(NEW.rsouserUid);
END IF;
END;$$

-- sets rso to inactive if less than 5 people are in the group, and removes admin if necessary
-- MOST LIKELY WONT WORK SINCE MYSQL APPARENTLY DOESNT LIKE IT WHEN YOU TRY TO ACTIVATE A TRIGGER ON A CASCADED DELETE
CREATE TRIGGER RSOStatusUpdateI AFTER DELETE ON rsouser
FOR EACH ROW BEGIN
IF ((SELECT COUNT(*) FROM rsouser M WHERE M.rsouserRid = OLD.rsouserRid) < 5)
THEN
	UPDATE rsos SET rsosStatus = "inactive" WHERE rsosId = OLD.rsouserRid;
	IF ((SELECT COUNT(*) FROM rsos N WHERE N.rsosOwnerId = OLD.rsouserUid) < 1)
	THEN
		DELETE FROM admins A WHERE A.adminsId = OLD.rsouserUid;
	END IF;
END IF;
END;$$

-- adds number of students to university
CREATE TRIGGER UniversityStudentCntA AFTER INSERT ON universityuser
FOR EACH ROW BEGIN
UPDATE universities SET universitiesNumStudents = universitiesNumStudents + 1 WHERE universitiesId = NEW.universityuserUniid;
END;$$

-- Subtracts number of students from university
-- MOST LIKELY WONT WORK SINCE MYSQL APPARENTLY DOESNT LIKE IT WHEN YOU TRY TO ACTIVATE A TRIGGER ON A CASCADED DELETE
CREATE TRIGGER UniversityStudentCntS AFTER DELETE ON universityuser
FOR EACH ROW BEGIN
UPDATE universities SET universitiesNumStudents = universitiesNumStudents - 1 WHERE universitiesId = OLD.universityuserUniid;
END;$$

DELIMITER ;