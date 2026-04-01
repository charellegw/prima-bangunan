package entities

import (
	"github.com/google/uuid"
	"github.com/shopspring/decimal"
)

type InvoiceDetail struct {
	ID        uuid.UUID       `gorm:"primaryKey;type:uuid;default:uuid_generate_v4()"`
	InvoiceID uuid.UUID       `gorm:"type:uuid;not null"`
	Invoice   *Invoice        `gorm:"foreignKey:InvoiceID;references:ID"`
	ItemID    uuid.UUID       `gorm:"type:uuid;not null"`
	Item      *Item           `gorm:"foreignKey:ItemID;references:ID"`
	Quantity  int             `gorm:"type:int;not null"`
	UnitPrice decimal.Decimal `gorm:"type:decimal(10,2);not null"`
	SubTotal  decimal.Decimal `gorm:"type:decimal(10,2);not null"`
}
