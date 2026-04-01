package entities

import (
	"github.com/google/uuid"
	"github.com/shopspring/decimal"
)

type SupplierPrice struct {
	ID         uuid.UUID        `gorm:"primaryKey;type:uuid;default:uuid_generate_v4()"`
	SupplierID uuid.UUID        `gorm:"type:uuid;not null"`
	Supplier   *Supplier        `gorm:"foreignKey:SupplierID;references:ID"`
	ItemID     uuid.UUID        `gorm:"type:uuid;not null"`
	Item       *Item            `gorm:"foreignKey:ItemID;references:ID"`
	UnitPrice  decimal.Decimal  `gorm:"type:decimal(10,2);not null"`
	DozenPrice *decimal.Decimal `gorm:"type:decimal(10,2);not null"` // lusin
}
