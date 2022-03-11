workspace {
    model {
        user = person "User" "User who visit the page"

        enterprise "Tulia CMS" {
            admin = person "Admin" "Administrator of CMS"

            tuliaCms = softwaresystem "Tulia CMS" "" {
                !adrs decisions
            }
        }

        user -> tuliaCms
        admin -> tuliaCms
    }

    views {
        systemlandscape "TuliaCmsEcosystem" "Tulia CMS Ecosystem" {
            include *
            autoLayout
        }

        styles {
        }

        theme default
    }
}
