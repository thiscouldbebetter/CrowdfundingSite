use Crowdfunding;

/* user "adam", password "Password_123" */
insert into User (Username, EmailAddress, NameFull, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive) values ('adam', 'adam@localhost.localdomain', 'Adam A. Adamson', '1147748628', '874c1d861559fa124a3948a947bc1f6564ea478b56e37b976a9ad25bbd67092e', null, 1);

/* user "beth", password "Password_123" */
insert into User (Username, EmailAddress, NameFull, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive) values ('beth', 'beth@localhost.localdomain', 'Bethany Barnes',  '1147748628', '874c1d861559fa124a3948a947bc1f6564ea478b56e37b976a9ad25bbd67092e', null, 1);

/* ProjectID, UserIDOrganizer, Name, GoalInUsd, TimeProposed, IsActive, Description */
insert into Project select 1, 1, 'Project Demo 1', 1000.00, NOW(), true, 'This is a demo project.';
insert into Project select 2, 2, 'Project Demo 2', 300.00, NOW(), true, 'This is a discontinued demo project.';

/* An inactive project test. */
insert into Project select 999, 1, 'Project Demo 999', 1000.00, NOW(), false, 'This is an inactive demo project.';

/* UserProjectPledgeID, UserID, ProjectID, PledgeAmountInUsd, TimePledged, IsActive) */
insert into UserProjectPledge select 1, 1, 1, 1.00, NOW(), true;
insert into UserProjectPledge select 2, 1, 2, 1.00, NOW(), false;

/* user "charlie", password "Password_123" */
insert into User (Username, EmailAddress, NameFull, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive) values ('charlie', 'charlie@localhost.localdomain', 'Charlie Charles', '1147748628', '874c1d861559fa124a3948a947bc1f6564ea478b56e37b976a9ad25bbd67092e', null, 1);

/* user "diane", password "Password_123" */
insert into User (Username, EmailAddress, NameFull, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive) values ('diane', 'diane@localhost.localdomain', 'Diane Delano', '1147748628', '874c1d861559fa124a3948a947bc1f6564ea478b56e37b976a9ad25bbd67092e', null, 1);
