package entities

import (
	"prima-bangunan-backend/constants"
	"time"

	"github.com/google/uuid"
	"github.com/shopspring/decimal"
)

type Shipment struct {
	ID        uuid.UUID                `gorm:"primaryKey;type:uuid;default:uuid_generate_v4()"`
	InvoiceID uuid.UUID                `gorm:"type:uuid;not null"`
	Invoice   *Invoice                 `gorm:"foreignKey:InvoiceID;references:ID"`
	Date      time.Time                `gorm:"type:timestamptz;not null"`
	Address   *string                  `gorm:"type:varchar(255);not null"`
	Fee       *decimal.Decimal         `gorm:"type:decimal(10,2)"`
	Status    constants.ShipmentStatus `gorm:"type:shipment_status;default:'PENDING';not null"`
	CreatedAt time.Time                `gorm:"type:timestamptz;default:current_timestamp;not null"`
	UpdatedAt time.Time                `gorm:"type:timestamptz;default:current_timestamp;not null"`
}
