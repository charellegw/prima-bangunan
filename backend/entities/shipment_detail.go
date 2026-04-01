package entities

import "github.com/google/uuid"

type ShipmentDetail struct {
	ID         uuid.UUID `gorm:"primaryKey;type:uuid;default:uuid_generate_v4()"`
	ShipmentID uuid.UUID `gorm:"type:uuid;not null"`
	Shipment   *Shipment `gorm:"foreignKey:ShipmentID;references:ID"`
	ItemID     uuid.UUID `gorm:"type:uuid;not null"`
	Item       *Item     `gorm:"foreignKey:ItemID;references:ID"`
	Quantity   int       `gorm:"type:int;not null"`
}
