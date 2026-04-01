package entities

import "github.com/google/uuid"

type Store struct {
	ID      uuid.UUID `gorm:"primaryKey;type:uuid;default:uuid_generate_v4()"`
	Name    string    `gorm:"type:varchar(100);not null"`
	OwnerID uuid.UUID `gorm:"type:uuid;not null"`
	User    *User     `gorm:"foreignKey:OwnerID;references:ID"`
}
