package constants

// INDIVIDUAL || BUSINESS
type ClientType string

const (
	Individual ClientType = "INDIVIDUAL"
	Business   ClientType = "BUSINESS"
)

var ClientTypes = map[ClientType]string{
	Individual: string(Individual),
	Business:   string(Business),
}

// ON PROGRESS || COMPLETED || CANCELLED || REFUNDED
type InvoiceStatus string

const (
	InvoiceStatusOnProgress InvoiceStatus = "ON PROGRESS"
	InvoiceStatusCompleted  InvoiceStatus = "COMPLETED"
	InvoiceStatusCancelled  InvoiceStatus = "CANCELLED"
	InvoiceStatusRefunded   InvoiceStatus = "REFUNDED"
)

var InvoiceStatuses = map[InvoiceStatus]string{
	InvoiceStatusOnProgress: string(InvoiceStatusOnProgress),
	InvoiceStatusCompleted:  string(InvoiceStatusCompleted),
	InvoiceStatusCancelled:  string(InvoiceStatusCancelled),
	InvoiceStatusRefunded:   string(InvoiceStatusRefunded),
}
