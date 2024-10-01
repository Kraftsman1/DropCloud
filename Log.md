# Keeping this log to track some of the things I've worked on and those that need changes or fixes.
## - Think "Changelog.md", but more for my own own consumption. a proper changelog file will be added later.


### October 1st.
- I have implemented the StorageProvider service but I still am not 100% sure it works as it should. Need to check more on that. 
- Had some issues during testing but was able to resolve those. The storage configuration saves in the DB, encrypted ofcourse. I am able to retrieve and decrypt it. 
- Run into some issues with the filesystem initializing but I was able to resolve that also. The fix was rewrite the getFileSystem function the model for StorageProvider and then install the *"league/flysystem-aws-s3-v3"* 
- I need to make some modifications to the connection test. Current method runs the test when the configuration is being saved. It failed ofcourse, on account of me using fake credentials. I'll haven to either setup a mock test or else use real credentials to see if it works. I'm thinking of adding a separate button to trigger a test for the connection, and then a separate button for saving. 
- Editing the configuration doesn't work well, but I can view all the added configurations and also delete them. Need to rework the delete prompt. Use a UI modal instead of the default browser prompt alert. 
- Also need to implement a check to ensure duplicate configurations cannot be saved.
- After the S3 option works perfectly, I'll implement other driver/storage provider options.
