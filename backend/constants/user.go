package constants

// VERIFIED || NOT_VERIFIED
type UserStatus string

const (
	Verified    UserStatus = "VERIFIED"
	NotVerified UserStatus = "NOT_VERIFIED"
)

var UserStatuses = map[UserStatus]string{
	Verified:    string(Verified),
	NotVerified: string(NotVerified),
}
