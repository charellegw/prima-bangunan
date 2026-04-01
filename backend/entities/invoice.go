package entities

import (
	"prima-bangunan-backend/constants"
	"time"

	"github.com/google/uuid"
	"github.com/shopspring/decimal"
)

type Invoice struct {
	ID         uuid.UUID               `gorm:"primaryKey;type:uuid;default:uuid_generate_v4()"`
	StoreID    uuid.UUID               `gorm:"type:uuid;not null"`
	Store      *Store                  `gorm:"foreignKey:StoreID;references:ID"`
	Date       time.Time               `gorm:"type:timestamptz;default:current_timestamp;not null"`
	ClientName string                  `gorm:"type:varchar(100);not null"`
	ClientType constants.ClientType    `gorm:"type:client_type;not null"`
	PaymentID  uuid.UUID               `gorm:"type:uuid;not null"`
	Payment    *Payment                `gorm:"foreignKey:PaymentID;references:ID"`
	Status     constants.InvoiceStatus `gorm:"type:invoice_status;default:'ON PROGRESS';not null"`
	Note       *string                 `gorm:"type:text;"`
	GrandTotal decimal.Decimal         `gorm:"type:decimal(10,2);not null"`
	Visible    bool                    `gorm:"default:true;not null"`
	CreatedAt  time.Time               `gorm:"type:timestamptz;default:current_timestamp;not null"`
	UpdatedAt  time.Time               `gorm:"type:timestamptz;default:current_timestamp;not null"`
	DeletedAt  *time.Time              `gorm:"type:timestamptz"`
}
