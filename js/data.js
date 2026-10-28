/* ==========================================================================
   Jaya's Collection — product & collection data
   Prices are intentionally "on enquiry" — confirm with the boutique.
   ========================================================================== */

const CATEGORIES = [
  {
    id: "necklaces",
    name: { en: "Necklaces", bn: "নেকলেস" },
    blurb: {
      en: "Chokers, haars and pendants for every neckline.",
      bn: "প্রতিটি নেকলাইনের জন্য চোকার, হার ও পেন্ডেন্ট।"
    },
    image: "assets/img/necklaces.jpg"
  },
  {
    id: "earrings",
    name: { en: "Earrings", bn: "কানের দুল" },
    blurb: {
      en: "Jhumkas, chandbalis and everyday studs.",
      bn: "ঝুমকা, চাঁদবালি আর রোজকার স্টাড।"
    },
    image: "assets/img/earrings.jpg"
  },
  {
    id: "bangles",
    name: { en: "Bangles & Bracelets", bn: "চুড়ি ও ব্রেসলেট" },
    blurb: {
      en: "Filigree kadas, bangle sets and delicate chains.",
      bn: "ফিলিগ্রি কাঁদা, চুড়ির সেট আর সূক্ষ্ম চেন।"
    },
    image: "assets/img/bangles.jpg"
  },
  {
    id: "bridal",
    name: { en: "Bridal Sets", bn: "ব্রাইডাল সেট" },
    blurb: {
      en: "Complete looks for the most photographed day.",
      bn: "জীবনের সবচেয়ে বিশেষ দিনের সম্পূর্ণ সাজ।"
    },
    image: "assets/img/bridal-set.jpg"
  },
  {
    id: "rings",
    name: { en: "Rings", bn: "আংটি" },
    blurb: {
      en: "Sculptural gold-plated rings, made to stack.",
      bn: "স্তরিত পরার জন্য গোল্ড-প্লেটেড আংটি।"
    },
    image: "assets/img/ring-gold.jpg"
  }
];

const PRODUCTS = [
  {
    slug: "noor-choker",
    category: "necklaces",
    name: { en: "Noor Gold-Plated Choker", bn: "নূর গোল্ড-প্লেটেড চোকার" },
    badge: "bestseller",
    image: "assets/img/necklaces.jpg",
    desc: {
      en: "A layered choker with soft floral detailing, finished in warm gold. It sits close to the collarbone and pairs beautifully with sarees, lehengas and open necklines.",
      bn: "নরম ফুলের নকশায় সাজানো স্তরিত চোকার, উষ্ণ সোনালি ফিনিশে। কলারবোনের কাছে বসে এবং শাড়ি, লেহেঙ্গা বা খোলা নেকলাইনের সঙ্গে দারুণ মানায়।"
    },
    style: { en: "Choker · Floral", bn: "চোকার · ফুলের নকশা" },
    occasion: { en: "Festive & evening", bn: "উৎসব ও সন্ধ্যা" }
  },
  {
    slug: "sonar-haar",
    category: "necklaces",
    name: { en: "Sonar Heritage Haar", bn: "সোনার হেরিটেজ হার" },
    badge: "bridal",
    image: "assets/img/haar.jpg",
    desc: {
      en: "A regal haar inspired by traditional Bengali bridal jewellery, with a broad bib of intricate gold-plated work. It brings depth and drama to a classic red-and-gold look.",
      bn: "প্রচলিত বাংলা ব্রাইডাল গহনা থেকে অনুপ্রাণিত রাজকীয় হার, চওড়া প্যানেলে সূক্ষ্ম গোল্ড-প্লেটেড কাজ। লাল-সোনালি চিরায়ত সাজে গভীরতা ও নাটকীয়তা আনে।"
    },
    style: { en: "Bib haar · Temple", bn: "বিব হার · টেম্পল" },
    occasion: { en: "Bridal & reception", bn: "ব্রাইডাল ও রিসেপশন" }
  },
  {
    slug: "meghna-pendant",
    category: "necklaces",
    name: { en: "Meghna Pendant Chain", bn: "মেঘনা পেন্ডেন্ট চেন" },
    badge: "new",
    image: "assets/img/pendant.jpg",
    desc: {
      en: "A single gold-plated pendant on a fine chain — the quiet piece you reach for every day. Layer it with a shorter chain or wear it alone.",
      bn: "সূক্ষ্ম চেনে একটি গোল্ড-প্লেটেড পেন্ডেন্ট — রোজকার নিরিবিলি পছন্দ। ছোট চেনের সঙ্গে স্তরিত করুন বা একা পরুন।"
    },
    style: { en: "Pendant · Minimal", bn: "পেন্ডেন্ট · মিনিমাল" },
    occasion: { en: "Everyday", bn: "রোজকার" }
  },
  {
    slug: "tara-layered",
    category: "necklaces",
    name: { en: "Tara Layered Necklace", bn: "তারা লেয়ার্ড নেকলেস" },
    badge: "new",
    image: "assets/img/layered.jpg",
    desc: {
      en: "Two delicate gold-plated strands designed to fall at different lengths, creating an effortless layered look without the tangling.",
      bn: "ভিন্ন দৈর্ঘ্যে পড়ার জন্য তৈরি দুটি সূক্ষ্ম গোল্ড-প্লেটেড স্তর — জট ছাড়াই সহজ লেয়ার্ড লুক।"
    },
    style: { en: "Layered · Contemporary", bn: "লেয়ার্ড · সমকালীন" },
    occasion: { en: "Everyday & brunch", bn: "রোজকার ও আড্ডা" }
  },
  {
    slug: "classic-jhumka",
    category: "earrings",
    name: { en: "Classic Gold Jhumka", bn: "ক্লাসিক গোল্ড ঝুমকা" },
    badge: "bestseller",
    image: "assets/img/jhumka.jpg",
    desc: {
      en: "The jhumka, refined. A dome of gold-plated filigree with a soft drop, light enough to wear from morning puja to evening dinner.",
      bn: "ঝুমকার পরিশীলিত রূপ। গোল্ড-প্লেটেড ফিলিগ্রির গম্বুজ ও নরম ঝুল — সকালের পুজো থেকে সন্ধ্যার ডিনার, সবখানেই আরামদায়ক।"
    },
    style: { en: "Jhumka · Filigree", bn: "ঝুমকা · ফিলিগ্রি" },
    occasion: { en: "Festive & everyday", bn: "উৎসব ও রোজকার" }
  },
  {
    slug: "ishani-chandbali",
    category: "earrings",
    name: { en: "Ishani Kundan Earring", bn: "ঈশানী কুন্দন কানের দুল" },
    badge: "",
    image: "assets/img/earrings.jpg",
    desc: {
      en: "A statement earring in a kundan-style setting, with deep red and green stones and a fringe of gold beads. Made for sarees, silks and evenings that deserve a little drama.",
      bn: "কুন্দন-ধাঁচের সেটিংয়ে স্টেটমেন্ট কানের দুল — গাঢ় লাল ও সবুজ পাথর, সঙ্গে সোনালি গুটির ঝালর। শাড়ি, সিল্ক আর বিশেষ সন্ধ্যার জন্য।"
    },
    style: { en: "Kundan · Statement", bn: "কুন্দন · স্টেটমেন্ট" },
    occasion: { en: "Festive & wedding", bn: "উৎসব ও বিয়ে" }
  },
  {
    slug: "bela-studs",
    category: "earrings",
    name: { en: "Bela Everyday Studs", bn: "বেলা এভরিডে স্টাড" },
    badge: "",
    image: "assets/img/earrings-alt.jpg",
    desc: {
      en: "Small, bright and endlessly wearable. These gold-plated studs are the quiet finish to an everyday outfit — and the perfect second piercing.",
      bn: "ছোট, উজ্জ্বল ও সবসময়ের সঙ্গী। এই গোল্ড-প্লেটেড স্টাড রোজকার সাজের নিরিবিলি সমাপ্তি — দ্বিতীয় পিয়ার্সিংয়ের জন্যও আদর্শ।"
    },
    style: { en: "Studs · Minimal", bn: "স্টাড · মিনিমাল" },
    occasion: { en: "Everyday", bn: "রোজকার" }
  },
  {
    slug: "rupa-drops",
    category: "earrings",
    name: { en: "Rupa Gold Drops", bn: "রূপা গোল্ড ড্রপস" },
    badge: "",
    image: "assets/img/drops.jpg",
    desc: {
      en: "A graceful drop silhouette with a textured gold-plated surface. Light on the ear, strong on presence.",
      bn: "টেক্সচার্ড গোল্ড-প্লেটেড পৃষ্ঠের মনোরম ড্রপ। কানে হালকা, উপস্থিতিতে দৃঢ়।"
    },
    style: { en: "Drops · Textured", bn: "ড্রপস · টেক্সচার্ড" },
    occasion: { en: "Evening & gifting", bn: "সন্ধ্যা ও উপহার" }
  },
  {
    slug: "shonar-bangle",
    category: "bangles",
    name: { en: "Shonar Filigree Bangle", bn: "সোনার ফিলিগ্রি চুড়ি" },
    badge: "bestseller",
    image: "assets/img/bangles.jpg",
    desc: {
      en: "An openwork gold-plated bangle with a fine filigree pattern. Wear one alone as a signature, or stack it with thinner bracelets.",
      bn: "সূক্ষ্ম ফিলিগ্রি নকশার ওপেনওয়ার্ক গোল্ড-প্লেটেড চুড়ি। একা পরুন সিগনেচার হিসেবে, বা পাতলা ব্রেসলেটের সঙ্গে স্তরিত করুন।"
    },
    style: { en: "Bangle · Filigree", bn: "চুড়ি · ফিলিগ্রি" },
    occasion: { en: "Festive & everyday", bn: "উৎসব ও রোজকার" }
  },
  {
    slug: "kajol-bangle-set",
    category: "bangles",
    name: { en: "Kajol Bangle Set", bn: "কাজল চুড়ির সেট" },
    badge: "",
    image: "assets/img/bangles-alt.jpg",
    desc: {
      en: "A set of slim gold-plated bangles made to be worn together. The mix of textures catches the light beautifully under wedding and festive lighting.",
      bn: "একসঙ্গে পরার জন্য পাতলা গোল্ড-প্লেটেড চুড়ির সেট। ভিন্ন টেক্সচারের মিশ্রণ বিয়ে বা উৎসবের আলোয় সুন্দরভাবে ঝলমলে।"
    },
    style: { en: "Bangle set", bn: "চুড়ির সেট" },
    occasion: { en: "Wedding & festive", bn: "বিয়ে ও উৎসব" }
  },
  {
    slug: "ratna-bangle",
    category: "bangles",
    name: { en: "Ratna Stone Bangle", bn: "রত্ন পাথরের চুড়ি" },
    badge: "",
    image: "assets/img/rings.jpg",
    desc: {
      en: "A single wide bangle in openwork gold-plated filigree, set with deep pink stones. One is enough — it holds its own against a plain saree.",
      bn: "ওপেনওয়ার্ক গোল্ড-প্লেটেড ফিলিগ্রির চওড়া চুড়ি, গাঢ় গোলাপি পাথরে সাজানো। একটি দিলেই যথেষ্ট — সাধারণ শাড়ির সঙ্গেও আলাদা করে নজর কাড়ে।"
    },
    style: { en: "Bangle · Stone-set", bn: "চুড়ি · পাথর বসানো" },
    occasion: { en: "Festive & wedding", bn: "উৎসব ও বিয়ে" }
  },
  {
    slug: "mou-bracelet",
    category: "bangles",
    name: { en: "Mou Chain Bracelet", bn: "মৌ চেন ব্রেসলেট" },
    badge: "new",
    image: "assets/img/bracelet.jpg",
    desc: {
      en: "A delicate gold-plated chain bracelet that sits lightly on the wrist. An easy gift — and an easy everyday signature.",
      bn: "কব্জিতে হালকা বসে থাকা সূক্ষ্ম গোল্ড-প্লেটেড চেন ব্রেসলেট। সহজ উপহার — আর রোজকার সহজ সিগনেচার।"
    },
    style: { en: "Chain bracelet", bn: "চেন ব্রেসলেট" },
    occasion: { en: "Everyday & gifting", bn: "রোজকার ও উপহার" }
  },
  {
    slug: "rani-kada",
    category: "bangles",
    name: { en: "Rani Gold Kada", bn: "রানী গোল্ড কাঁদা" },
    badge: "",
    image: "assets/img/bracelet-alt.jpg",
    desc: {
      en: "A broad kada with a sculpted gold-plated surface. It brings weight and presence to a simple kurta or a heavily embroidered lehenga.",
      bn: "খোদাই করা গোল্ড-প্লেটেড পৃষ্ঠের চওড়া কাঁদা। সাধারণ কুর্তা বা ভারী এমব্রয়ডারি লেহেঙ্গায় ওজন ও উপস্থিতি আনে।"
    },
    style: { en: "Kada · Sculpted", bn: "কাঁদা · খোদাই" },
    occasion: { en: "Festive", bn: "উৎসব" }
  },
  {
    slug: "noor-bridal-set",
    category: "bridal",
    name: { en: "The Noor Bridal Set", bn: "নূর ব্রাইডাল সেট" },
    badge: "bestseller",
    image: "assets/img/bridal-set.jpg",
    desc: {
      en: "A complete gold-plated bridal set — necklace, matching earrings and bangles — curated for the classic red-and-gold wedding palette.",
      bn: "সম্পূর্ণ গোল্ড-প্লেটেড ব্রাইডাল সেট — নেকলেস, মিলে যাওয়া কানের দুল ও চুড়ি — চিরায়ত লাল-সোনালি বিয়ের সাজের জন্য নির্বাচিত।"
    },
    style: { en: "Bridal set · Traditional", bn: "ব্রাইডাল সেট · ঐতিহ্যবাহী" },
    occasion: { en: "Wedding", bn: "বিয়ে" }
  },
  {
    slug: "rajwada-set",
    category: "bridal",
    name: { en: "Rajwada Kundan Set", bn: "রাজবাড়ি কুন্দন সেট" },
    badge: "new",
    image: "assets/img/kundan-set.jpg",
    desc: {
      en: "A full kundan-style set — necklace, earrings and maang tikka — with turquoise beads and pearl drops. Rich, architectural and made for the bride who loves heritage detailing.",
      bn: "সম্পূর্ণ কুন্দন-ধাঁচের সেট — নেকলেস, কানের দুল ও মাথার গহনা — ফিরোজা পাথর ও মুক্তোর ঝালরে। সমৃদ্ধ, স্থাপত্যের মতো আর ঐতিহ্যবাহী নকশা ভালোবাসা কনের জন্য।"
    },
    style: { en: "Kundan · Heritage", bn: "কুন্দন · হেরিটেজ" },
    occasion: { en: "Wedding & reception", bn: "বিয়ে ও রিসেপশন" }
  },
  {
    slug: "boron-bridal",
    category: "bridal",
    name: { en: "Boron Bridal Collection", bn: "বরন ব্রাইডাল কালেকশন" },
    badge: "bridal",
    image: "assets/img/bride-portrait.jpg",
    desc: {
      en: "A curated bridal look that balances statement pieces with refined detail — for the ceremony, the reception, or both.",
      bn: "স্টেটমেন্ট গহনা ও সূক্ষ্ম নকশার ভারসাম্যে নির্বাচিত ব্রাইডাল লুক — বিয়ের আসর, রিসেপশন বা দুটোর জন্যই।"
    },
    style: { en: "Bridal · Curated", bn: "ব্রাইডাল · নির্বাচিত" },
    occasion: { en: "Wedding", bn: "বিয়ে" }
  },
  {
    slug: "anaya-ring",
    category: "rings",
    name: { en: "Anaya Gold Ring", bn: "অনয়া গোল্ড রিং" },
    badge: "new",
    image: "assets/img/ring-gold.jpg",
    desc: {
      en: "A slim gold-plated band with a soft hammered texture. Designed to be worn alone or stacked with your everyday rings.",
      bn: "নরম হ্যামার্ড টেক্সচারের পাতলা গোল্ড-প্লেটেড ব্যান্ড। একা বা রোজকার আংটির সঙ্গে স্তরিত করে পরার জন্য।"
    },
    style: { en: "Band · Minimal", bn: "ব্যান্ড · মিনিমাল" },
    occasion: { en: "Everyday", bn: "রোজকার" }
  },
  {
    slug: "diya-ring",
    category: "rings",
    name: { en: "Diya Statement Ring", bn: "দিয়া স্টেটমেন্ট রিং" },
    badge: "",
    image: "assets/img/rings-display.jpg",
    desc: {
      en: "A sculptural gold-plated ring with a broader face — the finishing touch for evenings when the hands do the talking.",
      bn: "চওড়া মুখের ভাস্কর্যের মতো গোল্ড-প্লেটেড আংটি — সেই সন্ধ্যার শেষ স্পর্শ, যখন হাতই বলে ওঠে।"
    },
    style: { en: "Statement · Sculptural", bn: "স্টেটমেন্ট · ভাস্কর্য" },
    occasion: { en: "Evening & party", bn: "সন্ধ্যা ও পার্টি" }
  }
];

const WHATSAPP_NUMBER = "919831917368";
const STORE = {
  phone: "+91 98319 17368",
  phoneHref: "tel:+919831917368",
  email: "jayalahiri1972@gmail.com",
  address: {
    en: "Ballygunge New Market (AC), 1st Floor, Shop 56, near Gariahat More, Kolkata 700019",
    bn: "বালিগঞ্জ নিউ মার্কেট (এসি), ১ম তলা, দোকান ৫৬, গড়িয়াহাট মোড়ের কাছে, কলকাতা ৭০০০১৯"
  },
  map: "https://www.google.com/maps/search/?api=1&query=Jaya%27s+Collection+Ballygunge+New+Market+Kolkata",
  facebook: "https://www.facebook.com/people/Jayas-Collection/61576726888077/"
};
