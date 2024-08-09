use Crowdfunding;

/* user "adam", password "Password_123" */
insert into User (Username, EmailAddress, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive) values ('adam', 'adam@localhost.localdomain', '1147748628', '874c1d861559fa124a3948a947bc1f6564ea478b56e37b976a9ad25bbd67092e', null, 1);

/* ProjectID, UserIDOrganizer, Name, FundingThresholdInUsd, IsActive, Description */
insert into Project select 1, 1, 'Project Demo 1', 1000.00, true, 'This is a demo project.';

/* An inactive project test. */
insert into Project select 999, 1, 'Project Demo 999', 1000.00, false, 'This is an inactive demo project.';

/* UserProjectPledgeID, UserID, ProjectID, PledgeAmountInUsd, TimePledged)
insert into UserProjectPledge select 1, 1, 1, 1.00, NOW() );

/* user "beth", password "Password_123" */
insert into User (Username, EmailAddress, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive) values ('beth', 'beth@localhost.localdomain', '1147748628', '874c1d861559fa124a3948a947bc1f6564ea478b56e37b976a9ad25bbd67092e', null, 1);

/* user "charlie", password "Password_123" */
insert into User (Username, EmailAddress, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive) values ('charlie', 'charlie@localhost.localdomain', '1147748628', '874c1d861559fa124a3948a947bc1f6564ea478b56e37b976a9ad25bbd67092e', null, 1);

/* user "diane", password "Password_123" */
insert into User (Username, EmailAddress, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive) values ('diane', 'diane@localhost.localdomain', '1147748628', '874c1d861559fa124a3948a947bc1f6564ea478b56e37b976a9ad25bbd67092e', null, 1);
