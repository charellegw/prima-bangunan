package entities

import (
	"time"

	"github.com/google/uuid"
	"github.com/shopspring/decimal"
)

type Item struct {
	ID             uuid.UUID        `gorm:"primaryKey;type:uuid;default:uuid_generate_v4()"`
	StoreID        uuid.UUID        `gorm:"type:uuid;not null"`
	Store          *Store           `gorm:"foreignKey:StoreID;references:ID"`
	ItemCategoryID uuid.UUID        `gorm:"type:uuid;not null"`
	ItemCategory   *ItemCategory    `gorm:"foreignKey:ItemCategoryID;references:ID"`
	Name           string           `gorm:"type:varchar(100);not null"`
	Brand          string           `gorm:"type:varchar(100);not null"`
	Stock          *int             `gorm:"type:int;"`
	Description    *string          `gorm:"type:text"`
	Price          *decimal.Decimal `gorm:"type:decimal(10,2)"`
	ResellerPrice  *decimal.Decimal `gorm:"type:decimal(10,2)"`
	ImageUrl       *string          `gorm:"type:text"`
	CreatedAt      time.Time        `gorm:"type:timestamptz;default:current_timestamp;not null"`
	UpdatedAt      time.Time        `gorm:"type:timestamptz;default:current_timestamp;not null"`
}
