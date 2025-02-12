CREATE TABLE IF NOT EXISTS PasswordResets (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Token VARCHAR(255) NOT NULL,
    Created DATETIME NOT NULL,
    FOREIGN KEY (UserID) REFERENCES Users(ID) ON DELETE CASCADE
);

CREATE INDEX idx_password_resets_token ON PasswordResets(Token);
CREATE INDEX idx_password_resets_created ON PasswordResets(Created);