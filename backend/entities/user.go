package entities

import (
	"time"

	"github.com/google/uuid"
	"prima-bangunan-backend/constants"
)

type User struct {
	ID                  uuid.UUID            `gorm:"primaryKey;type:uuid;default:uuid_generate_v4()"`
	Name                string               `gorm:"type:varchar(100);not null"`
	Email               string               `gorm:"type:varchar(100);not null;uniqueIndex"`
	Password            string               `gorm:"type:varchar(100);not null"`
	ResetPasswordToken  *string              `gorm:"type:text"`
	ResetPasswordExpiry *time.Time           `gorm:"type:timestamptz"`
	Status              constants.UserStatus `gorm:"type:user_status;default:'NOT_VERIFIED';not null"`
	CreatedAt           time.Time            `gorm:"type:timestamptz;default:current_timestamp;not null"`
	UpdatedAt           time.Time            `gorm:"type:timestamptz;default:current_timestamp;not null"`
}
