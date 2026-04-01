package entities

import (
	"time"

	"github.com/google/uuid"
)

type InvoiceSupplier struct {
	ID uuid.UUID `gorm:"primaryKey;type:uuid;default:uuid_generate_v4()"`
	StoreID    uuid.UUID `gorm:"type:uuid;not null"`
	Store      *Store    `gorm:"foreignKey:StoreID;references:ID"`
	SupplierID uuid.UUID `gorm:"type:uuid;not null"`
	Supplier   *Supplier `gorm:"foreignKey:SupplierID;references:ID"`
	Status     string    `gorm:"type:invoice_status;default:'ON PROGRESS';not null"`
	Date time.Time `gorm:"type:timestamptz;default:current_timestamp;not null"`
	Note *string `gorm:"type:text;"`
	CreatedAt time.Time `gorm:"type:timestamptz;default:current_timestamp;not null"`
	UpdatedAt time.Time `gorm:"type:timestamptz;default:current_timestamp;not null"`
}