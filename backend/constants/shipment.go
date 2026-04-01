package constants

// PENDING || SHIPPING || DELIVERED || RETURNED
type ShipmentStatus string

const (
	ShipmentStatusPending   ShipmentStatus = "PENDING"
	ShipmentStatusShipping  ShipmentStatus = "SHIPPING"
	ShipmentStatusDelivered ShipmentStatus = "DELIVERED"
	ShipmentStatusReturned  ShipmentStatus = "RETURNED"
)

var ShipmentStatuses = map[ShipmentStatus]string{
	ShipmentStatusPending:   string(ShipmentStatusPending),
	ShipmentStatusShipping:  string(ShipmentStatusShipping),
	ShipmentStatusDelivered: string(ShipmentStatusDelivered),
	ShipmentStatusReturned:  string(ShipmentStatusReturned),
}
