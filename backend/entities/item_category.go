package entities

import "github.com/google/uuid"

type ItemCategory struct {
	ID   uuid.UUID `gorm:"primaryKey;type:uuid;default:uuid_generate_v4()"`
	Name string    `gorm:"type:varchar(100);not null;uniqueIndex"`
}
