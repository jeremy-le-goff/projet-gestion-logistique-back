<?php

namespace App\DataFixtures\Provider;


class Projet14
{
    // Listing of 15 associations
    private $organizations = [
        "Secours Catholique",
        "Restos du Cœur",
        "Emmaüs France",
        "Fondation Abbé Pierre",
        "Croix-Rouge française",
        "ATD Quart Monde",
        "Les Petits Frères des Pauvres",
        "Secours Populaire français",
        "Action contre la Faim",
        "Médecins du Monde",
        "Entraide et Fraternité",
        "Fondation de l'Armée du Salut",
        "La Chorba",
        "Les Restos Bébés du Cœur",
        "Le Samusocial de Paris",
    ];
    
    // List of 50 categories
    private $categories = [
        "Fruits et légumes",
        "Viandes, poissons",
        "Charcuteries, traiteur",
        "Produits frais",
        "Surgelés",
        "Epicerie salée",
        "Epicerie sucrée",
        "Pains, Viennoiseries et Pâtisseries",
        "Boissons sans alcool"
    ];
    
    // List of the 4 types of preservation
    private $conservation_type = [
        "Frais",
        "Ultra-Frais",
        "Surgelé",
        "Ambiant",
    ];
    
    // List of brands
    private $brands = [
        "Danone",
        "Nestlé",
        "Kellogg's",
        "Bonduelle",
        "Herta",
        "Bel (La Vache Qui Rit)",
        "Blédina",
        "Panzani",
        "Lustucru",
        "Fleury Michon",
        "Coca-Cola",
        "Pepsi",
        "Evian",
        "Perrier",
        "Orangina",
        "Tropicana",
        "L'Oréal",
        "Garnier",
        "Nivea",
        "Dove",
        "Sanex",
        "Colgate",
        "Signal",
        "Gillette",
        "Head & Shoulders",
        "Pampers",
        "Dodie",
        "Mustela",
        "Blédina",
    ];
    
    // List of 100 product names
    private $products = [
        "Pain de mie",
        "Yaourt à la vanille",
        "Céréales au chocolat",
        "Jambon blanc",
        "Lait demi-écrémé",
        "Tomates en conserve",
        "Pâtes spaghetti",
        "Sauce tomate",
        "Pommes de terre",
        "Fromage emmental",
        "Salade iceberg",
        "Jus d'orange",
        "Poulet rôti",
        "Biscuits au chocolat",
        "Eau minérale",
        "Café moulu",
        "Thon en boîte",
        "Bœuf haché",
        "Riz basmati",
        "Fraises",
        "Miel",
        "Cornflakes",
        "Beurre doux",
        "Confiture de fraises",
        "Carottes",
        "Filet de saumon",
        "Biscottes",
        "Lentilles en conserve",
        "Compote de pommes",
        "Crème fraîche",
        "Chocolat noir",
        "Pois chiches en boîte",
        "Poisson pané",
        "Mayonnaise",
        "Pain complet",
        "Soda cola",
        "Olive noire",
        "Pomme de terre en purée",
        "Haricots verts",
        "Moutarde",
        "Pizza surgelée",
        "Thé vert",
        "Oeufs",
        "Saumon fumé",
        "Champignons en conserve",
        "Aubergines",
        "Galette de riz",
        "Eau gazeuse",
        "Muesli",
        "Crêpes",
        "Pamplemousse",
        "Poisson frais du jour",
        "Bacon",
        "Céréales au miel",
        "Pain aux céréales",
        "Pâte à pizza",
        "Fromage de chèvre",
        "Kiwi",
        "Poulet en morceaux",
        "Biscuits au miel",
        "Compote de poires",
        "Boulettes de viande",
        "Lait de soja",
        "Concombre",
        "Beurre salé",
        "Céréales au fruit",
        "Crevettes cuites",
        "Pain aux noix",
        "Sauce pesto",
        "Pommes",
        "Amandes",
        "Eau de coco",
        "Poulet cuit en tranches",
        "Biscuits au beurre",
        "Compote de pêches",
        "Pommes de terre rissolées",
        "Fromage gouda",
        "Framboises",
        "Sirop d'érable",
        "Tomates cerises",
        "Pâtes penne",
        "Sauce Alfredo",
        "Courgettes",
        "Cidre",
        "Barres de céréales",
        "Crevettes crues",
        "Pain aux raisins",
        "Sauce bolognaise",
        "Poires",
        "Noix",
        "Jus de pomme",
        "Poulet grillé",
        "Biscuits aux amandes",
        "Compote de cerises",
        "Pommes de terre au four",
        "Fromage roquefort",
        "Myrtilles",
        "Moutarde à l'ancienne",
        "Pommes de terre douces",
        "Fromage feta",
        "Pesto de tomates séchées",
        "Mangues",
        "Jus de raisin",
        "Poulet mariné",
        "Biscuits aux noix",
        "Compote de mangues",
        "Pommes de terre écrasées",
        "Fromage bleu",
        "Melon",
        "Sirop de menthe",
        "Poulet rôti en tranches",
        "Biscuits à l'avoine",
        "Compote de fruits rouges",
        "Pommes de terre sautées",
        "Fromage camembert",
        "Nectarines",
        "Vinaigrette",
        "Poulet frit",
        "Biscuits au citron",
        "Compote de framboises",
        "Poivrons",
        "Fromage suisse",
        "Ananas",
        "Vinaigre balsamique",
        "Poulet croustillant",
        "Biscuits au gingembre",
        "Compote de myrtilles",
        "Ail",
        "Feta marinée",
        "Avocats",
        "Vinaigrette à la moutarde",
        "Poulet rôti en morceaux",
        "Biscuits aux pépites de chocolat",
        "Compote de pruneaux",
        "Oignons",
        "Fromage de chèvre mariné",
        "Bananes",
        "Vinaigrette au miel",
        "Poulet grillé en morceaux",
        "Biscuits à la cannelle",
        "Compote de poires et cannelle",
        "Lait d'amande",
        "Framboises surgelées",
        "Brocolis",
        "Sauce soja",
        "Poulet épicé",
        "Biscuits au caramel",
        "Compote de pommes et cannelle",
        "Lait de noix de coco",
        "Myrtilles surgelées",
        "Chou-fleur",
        "Sauce barbecue",
        "Poulet au curry",
        "Biscuits au chocolat blanc",
        "Compote de pêches et mangues",
        "Lait de riz",
        "Mangues surgelées",
        "Asperges",
        "Sauce teriyaki",
        "Poulet à l'ail",
        "Biscuits sans sucre ajouté",
        "Compote de fruits tropicaux",
        "Lait de soja à la vanille",
        "Fraises surgelées",
        "Poireaux",
        "Sauce curry",
        "Poulet au citron",
        "Barres énergétiques",
        "Compote de fruits exotiques",
        "Lait d'avoine",
        "Fruits des bois surgelés",
        "Épinards",
        "Sauce aux herbes",
        "Poulet au paprika",
        "Barres protéinées",
        "Compote de fruits de la passion",
        "Lait de noisette",
        "Compote de fruits tropicaux",
        "Lait de soja à la vanille",
        "Fraises surgelées",
        "Poireaux",
        "Sauce curry",
        "Poulet au citron",
        "Barres énergétiques",
        "Compote de fruits exotiques",
        "Lait d'avoine",
        "Fruits des bois surgelés",
        "Épinards",
        "Sauce aux herbes",
        "Poulet au paprika",
        "Barres protéinées",
        "Compote de fruits de la passion",
        "Lait de noisette",
    ];
    
    private $user = [
        "super admin" => [
            "email" => "superadmin@oclock.io",
            "role" => ["ROLE_SUPERADMIN"],
            "password" => "superadmin"
        ],
        "admin" => [
            "email" => "admin@oclock.io",
            "role" => ["ROLE_ADMIN"],
            "password" => "admin"
        ],
        "manager" => [
            "email" => "manager@oclock.io",
            "role" => ["ROLE_MANAGER"],
            "password" => "manager"
        ],
        "logistician" => [
            "email" => "logistician@oclock.io",
            "role" => ["ROLE_LOGISTICIAN"],
            "password" => "logistician"
        ],
        "user" => [
            "email" => "user@oclock.io",
            "role" => [],
            "password" => "user"
            ]
        ];
        
        private $status = ["désactivé", "activé"];
        
        // Types of conditioning of products
        private $conditioning = [
            "palettes",
            "cartons",
            "barquettes",
            "sachets",
            "bidons",
            "boîtes de conserves",
            "bouteilles",
            "cageots",
            "caisse en carton",
            "sacs en plastique"   
        ];
        
        /**
        * Retourn random organization
        */
        public function organizationRandom()
        {
            // Nb of organization
            return $this->organizations[mt_rand(0, 14)];
        }
        
        /**
        * Retourn random category
        */
        public function categoryRandom()
        {
            // Nb of categories 53
            return $this->categories[mt_rand(0, 8)];
        }
        
        /**
        * Retourn random brand
        */
        public function brandRandom()
        {
            // Nb of brands
            return $this->brands[mt_rand(0, 28)];
        }
        /**
        * Retourn random conservationType
        */
        public function conservationTypeRandom()
        {
            // Nb of conservation_type 4
            return $this->conservation_type[mt_rand(0, 3)];
        }
        
        /**
        * Retourn random product
        */
        public function productRandom()
        {
            // Nb of products 99
            return $this->products[mt_rand(0, 99)];
        }
        
        /**
        * Get a  user, available roles : super admin, admin, manager, logistician, user
        * @param string $role the role of the user
        * @return array a user
        */
        public function userRandom()
        {
            return $this->user[array_rand($this->user)];
        }
        
        public function statusRandom()
        {
            
            return $this->status[array_rand($this->status)];
        }
        
        /**
        * Return random conditioning
        */
        public function conditioningRandom()
        {
            // 10 types of condioning 
            return $this->conditioning[mt_rand(0, 9)];
        }
    }
