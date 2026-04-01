package entities

import (
	"time"

	"github.com/google/uuid"
	"github.com/shopspring/decimal"
)

type PaymentHistory struct {
	ID        uuid.UUID       `gorm:"primaryKey;type:uuid;default:uuid_generate_v4()"`
	PaymentID uuid.UUID       `gorm:"type:uuid;not null"`
	Payment   *Payment        `gorm:"foreignKey:PaymentID;references:ID"`
	Date      time.Time       `gorm:"type:timestamptz;default:current_timestamp;not null"`
	Amount    decimal.Decimal `gorm:"type:decimal(10,2);not null"`
	Method    string          `gorm:"type:varchar(100);not null"`
}
