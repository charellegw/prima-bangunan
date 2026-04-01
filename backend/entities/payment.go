package entities

import "time"

type Payment struct {
	ID        int        `gorm:"primaryKey;type:uuid;default:uuid_generate_v4()"`
	StoreID   int        `gorm:"type:uuid;not null"`
	Store     *Store     `gorm:"foreignKey:StoreID;references:ID"`
	Status    string     `gorm:"type:payment_status;default:'UNPAID';not null"`
	DueDate   time.Time  `gorm:"type:timestamp;not null"`
	Visible   bool       `gorm:"type:boolean;default:true;not null"`
	CreatedAt time.Time  `gorm:"type:timestamptz;default:current_timestamp;not null"`
	UpdatedAt time.Time  `gorm:"type:timestamptz;default:current_timestamp;not null"`
	DeletedAt *time.Time `gorm:"type:timestamptz"`
}
