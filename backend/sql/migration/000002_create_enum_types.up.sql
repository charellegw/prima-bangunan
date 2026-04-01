DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'client_type') THEN
        CREATE TYPE client_type AS ENUM ('INDIVIDUAL', 'BUSINESS');
    END IF;

    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'payment_status') THEN
        CREATE TYPE payment_status AS ENUM ('UNPAID', 'PAID', 'REFUNDED', 'OVERDUE');
    END IF;

    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'user_status') THEN
        CREATE TYPE user_status AS ENUM ('NOT_VERIFIED', 'VERIFIED');
    END IF;

    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'shipment_status') THEN
        CREATE TYPE shipment_status AS ENUM ('PENDING', 'SHIPPING', 'DELIVERED', 'RETURNED');
    END IF;

    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'invoice_status') THEN
        CREATE TYPE invoice_status AS ENUM ('ON PROGRESS', 'COMPLETED', 'CANCELLED', 'REFUNDED');
    END IF;
END $$;