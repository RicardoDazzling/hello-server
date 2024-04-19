# Task List

- [x] JWT with uuid and email to logged users;  
- [x] Do the services retrieve the entity;
- [x] [Token entity](## "token, uuid, email, name, time") and [token DAL](## "Don't expire. Only token column");   
- [x] Disable user route;
- [x] Add interfaces for each repeated class;
- [x] Message route, [message entity, message service](## "from, to, content, send, received, read") and [message DAL](## "will stay a month in the database");  
- [x] Files route, [file entity, file service](## "from, to, content, send, received, read, open") and [file DAL](## "will stay 7 days in the database");  
- [x] Remove outdated files and messages;
- [x] Registration and Login with mail verification;
- [x] Call route, [call entity, call service](## "from, to, image, audio") and call DAL;
- [x] Writing route, [writing entity, writing service](## "from, to"); and writing DAL;
- [x] Lost route, [lost entity, lost service](## "from, to, type, send") and [lost DAL](## "will stay a month in the database");
- [x] Events [static file](## "writing, message, file, call, lost");
- [ ] Move google auth JSON to a static private folder;
- [ ] Debug.