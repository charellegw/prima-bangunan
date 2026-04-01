package entities

import (
	"time"

	"github.com/google/uuid"
)

type Supplier struct {
	ID         uuid.UUID `gorm:"primaryKey;type:uuid;default:uuid_generate_v4()"`
	Name       string    `gorm:"type:varchar(100);not null"`
	Location   *string   `gorm:"type:varchar(255)"`
	Visible    bool      `gorm:"type:boolean;default:true;not null"`
	CreatedAt  time.Time `gorm:"type:timestamptz;default:current_timestamp;not null"`
	UpdatedAt  time.Time `gorm:"type:timestamptz;default:current_timestamp;not null"`
	DeletedAt  *time.Time `gorm:"type:timestamptz"`
}
