<?PHP
include_once '../lib/utils.php';
  //include_once 'parser.php';
include_once '../script_lib.php';


$quotes = 
  array(
  array(58605 , 'Vijay Dinanath Chauhan…pura naam. Baap ka naam…Dinanath Chauhan. Maa ka naam…Suhasini Chauhan. Gaon…Mandwa. Umar…chattis saal, nau mahina, aant din…solva ghanta chaaloo hai…ain?'), array(
      58538 , 'Daaru mat pee, mat pee, mat pee!'), array(
      19205 , 'Maine tumse kitni baar kaha hai, Pushpa, mujhse yeh aansoon nahin dekhe jaate...I hate tears!'), array(
      3068 , 'Crime Master Gogo naam hai mera! Aankhen nikaal ke gotiyan khelta hoon main!'), array(
      3068 , 'Gogi jee! Aap ka ghaagra.'), array(
      3068 , 'Main Teja hoon kyon ke mera naam bhi Teja hai.'), array(
      3068 , 'Shakal se to bidi ke kaarkhaane ka mazdoor lagta hai.'), array(
      3068 , 'Saala choosawa aam.'), array(
      3068 , 'Circus ka retard bandar lagta hai.'), array(
      3068 , 'Bhabhi hogi teri, aur shaadi hogi meri!'), array(
      3068 , 'Muscle dekha muscle? Mussal ke rakhdoonga!'), array(
      3068 , 'Sikka udaate hain. Jo jeeta woh sikander. Aur jo haara woh bandar.'), array(
      3068 , 'Galti se mishtake ho gaya, sir.'), array(
      3068 , 'Aaya hoon…kuch to loot kar jaaoonga! Khandaani chor hoon main, khandaani!'), array(
      3068 , 'Kiske mama ki gun hai?'), array(
      9403 , 'Bhagwaan ke liye chhod do mujhe!'), array(
      8439 , 'Pandra to mile jaana hai…saade saath yeh chalega, saade saath main chaloonga!'), array(
      8439 , 'Tumhare paas popat hai? Le! Hum ne to kabhi dekha hi nahin hai!'), array(
      8439 , 'Main bol raha tha ke ek baar agar mera dimaagh garam ho gaya na…to thanda bhi phat-a-phat ho jaata hai!'), array(
      11623 , 'Kabhi kabhi jeetne ke liye kuch haarna bhi parta hai. Aur haar kar jeetne vaale ko…baazigar kehtein hain!'), array(
      11623 , 'Udne ki baat parindey karte hain, Madan Chopra. Toothey hue par nahin!'), array(
      2865 , 'Bread pakode ki kasam.'), array(
      15899 , 'Prem naam hai mera…Prem Chopra.'), array(
      15899 , 'Aaj tum dono ko khone ki naubat aa gayi. Ab mehsoos hua…ke bache maa-baap ke liye kitne keemti hotein hain!'), array(
      15899 , 'Humein saath jeena naseeb nahin hua, daddy! Magar hum saath mar to sakte hain!'), array(
      14849 , 'Mujh par ek ehsaan karna…ke mujh par koi ehsaan na karna.'), array(
      1696 , 'Jao aur apne aap se, is zindagi se, apne khuda se, aur har us insaan se jisne tum par bharosa nahin kiya…apne sattar minute chheeno!'), array(
      1696 , 'Is team mein sirf wohi players chahiye jo pehle India ke liye khel rahein hain, uske baad is team mein apni saathiyon ke liye, aur uske baad agar thodi bahut jaan bachh jaaye to apne liye.'), array(
      13836 , 'Thapad se darr nahin lagta, saab. Pyaar se lagta hai.'), array(
      13836 , 'Hum yahaan ke Robin Hood hain…Robin Hood Pandey.'), array(
      13836 , 'Arey "kamini" se yaad aaya…Tiwari ji, Bhabhi ji kaisi hain?'), array(
      13836 , 'Varna, Chedi Singh, hum tum me itne ched karenge…ke conpyooj ho jaaoge…ke saans kahaan se lein…aur paadein kahaan se!'), array(
      2182 , 'Aise khilone bazaar mein bahut bikte hain. Magar is se khelne ke liye jo jigar chahiye na, woh duniya ke kisi bazaar mein nahin bikta. Mard usse le kar paida hota hai.'), array(
      2182 , 'Jab yeh dhai kilo ka haath kisi par padta hai na…to aadmi uthta nahin…uth jaata hai.'), array(
      2182 , 'Taareekh par taareekh, taareekh par taareekh, taareekh par taareekh, taareekh par taareekh milti rahi hai…lekin insaaf nahin mila, milord! Insaaf nahin mila!'), array(
      6887 , 'I love you, K-K-K-Kiran!'), array(
      8260 , 'Mere paas Maa hai!'), array(
      8260 , 'Apni Maa ko khareedne ki koshish mat kar…tu abhi itna amir nahin hua, beta, ke apni Maa ko khareed sake!'), array(
      8260 , 'Main aaj bhi phenke hue paise nahin uthata.'), array(
      58385 , 'Man…did you drink all my juice?'), array(
      16360 , 'Baabuji ne kaha gaon chhod do. Sab ne kaha Paro ko chhod do. Paro ne kaha sharaab chhod do. Aaj tumne keh diya haveli chhod do. Ek din aayega jab Woh kahega…duniya hi chhod do.'), array(
      3484 , 'Are you, like, checking me out?'), array(
      15693 , 'Mein tumse aur sirf tumse pyaar karta hoon. Meri har saans, meri har dhadkan, mere har pal mein tum ho aur sirf tum ho, Shalini. Mujhe yakeen hai ke main sirf is liye janma hoon ke tumse pyaar kar sakoon. Aur tum sirf is liye ke ek din meri ban jaao. Tum meri ho, Shalini. Aur apne dil se poochhogi…to jaanlogi…ke main sach keh raha hoon!'), array(
      15693 , 'Aaakash!'), array(
      19326 , 'Mar gaya Rahul!'), array(
      19326 , '…Aur paas…'), array(
      19326 , 'Rahul…naam to suna hoga?'), array(
      1661 , 'Koi baat nahin, senorita. Koi baat nahin. Bade bade deshon mein aisi choti choti baatein hoti rehti hain.'), array(
      1661 , 'I hate girls.'), array(
      1661 , 'You are not only genious, you are indigenous!'), array(
      1661 , 'Main ek Hindustani hoon…aur main jaanta hoon ke ek Hindustani ladki ki izzat kya hoti hai.'), array(
      1661 , 'Raj, agar yeh tujhe pyaar karti hai, to yeh palat ke dekhegi…palat…palat…'), array(
      1661 , 'Sir…aakhir ek Hindustani hi ek Hindustani ke kaam aa sakte chasma nahin hai, kaise dikhega! Jaa! Chasma laa mera!'), array(
      19577 , 'Khadak Singh ke khadakne se khadakti hai khidkiyaan!!!'), array(
      19577 , 'Aur jo kuwaa hai na? Uske andar bahar, dono side pe mere ko wallpaper hona, dono sidmne nahin jhuk sakta, jailer sahab. Jhukega to Uski chaukhat pe…ya Uske darbaar mein jhukega.'), array(
      18920 , 'Alvida nahin…alvida kehne se phir milne ki umeed hi mar jaati hai. Kya pata? Phir milein?'), array(
      18920 , 'I like blue.'), array(
      58539 , 'Kabhi raste mein mil jaao to katra kar guzar jaana / Humein is tarha takna jaise pehchaana nahin tur hai…'), array(
      14264 , 'Ishvar ki praatna k'), array(
      14264 , 'Suno…jiyo…khush raho…muskuraao…kya pata…kal ho naa ho!'), array(
      14264 , 'Chey din, ladki in.'), array(
      14264 , 'Hey! Kantaben!'), array(
      9961 , 'Main "fuh" ko "fuh" bolta hoon.'), array(
      9961 , 'Life badi kutti cheef hai.'), array(
      9961 , 'Zindagi mein hamari watt if fe nahin lagti ke hum kaunfa raafta chunte hain…watt lagti hai if fe ke hum kaunfa raafta chhodtein hain.'), array(
      9961 , 'Paifa kamaane ke do raafte: ek, fortcut…aur doofra, chota fortcut.'), array(
      5550 , 'Bhaag, Arjun, bhaag!'), array(
      1663 , 'Pyaar dosti hai! Agar woh meri sab se achi dost nahin ban sakti, to main us se kabhi pyaar kar hi nahin sakta…kyon ki dosti bina to pyaar hota hi nahin!'), array(
      1663 , 'Hum ek hi baar jeetein hain...ek baar martein hain...shaadi bhi ek hi baar hoti hai…aur pyaar bhi ek hi baar hota hai…baar baar nahin hota…'), array(
      1663 , 'Kuch kuch hota hai…tum nahin samjhogi…'), array(
      1663 , 'Tussi jaa rahe ho?'), array(
      1663 , 'Miss Braganza, a ha!'), array(
      1663 , 'Squeeze me…'), array(
      1663 , 'Naw…she\'s not my type…'), array(
      1663 , 'Hey…Rahul Khanna kisi nahin darrta.'), array(
      1663 , 'Khelna nahin aata hai to cheating to mat karo!'), array(
      1663 , 'Girls cannot play basketball.'), array(
      1663 , 'Aw, excusi moi…main ladkiyon ke peeche nahin bhaagta…ladkiyan mere peeche bhaagti hain.'), array(
      1663 , 'Rahul aur Anjali ka phir se jhagda ho gaya!'), array(
      1663 , 'Mard ka sar sirf teen auraton ke saamne jhukta hai. Ek Mummy ke aage…apni Maa ke saamne. Ek Durga Maa ke saamne. Aur...'), array(
      7544 , 'Haan, Arjan…hum sapna dekhat hai…aur sapne vohi saakar kar paave hai…jo unhe dekhat hai!'), array(
      7544 , 'Ye sirf khel nahin hai jo hum kisi ke mauj manoranjan ke liye khelat hai! Yeh ek ladaai hai jo humka jeetni hai!'), array(
      7544 , 'Kachra khelega!'), array(
      7544 , 'Sarat manjoor hai.'), array(
      3126 , 'Sorry about the sari.'), array(
      3126 , 'Makko man!'), array(
      1403 , 'Prem, ek ladka ladki kabhi dost nahin hote!'), array(
      1403 , 'Cigarette smoking is injurious to health. Sehat ke liye haanikaarak hota hai.'), array(
      1403 , 'Dosti ka ek usool hai, mister...no "sorry"...no "thank you"…okay?'), array(
      1403 , 'Dosti ki hai…nibhaani to padegi.'), array(
      1403 , 'Abhi mood nahin hai.'), array(
      13905 , 'Jo mard hota hai, usse dard nahin hota, memsaab.'), array(
      5995 , 'Janamdin mubarak ho…Abba!'), array(
      8956 , 'Parampara, pratishtha, anushaasan. Yeh is Gurukul ke teen stambh hain.'), array(
      8956 , 'Maaf kijiyega, sir. Par jahan se main dekh raha hoon, aap haar gaye.'), array(
      8956 , 'Is imaarat ki neev itni mazboot hai ke koi Raj Aryan, haathon mein violin aur chehre pe muskaan liye yahaan uski ek bhi inth hilaane ke liye kadam nahin rak sakta. Kabhi nahin, Mr. Raj Aryan…kabhi nahin.'), array(
      8956 , 'Aap ne abhi tak sirf darr ki taaqat dekhi hai…mohabbat ki taaqat to aap ne abhi tak dekhi hi nahin. Mohabbat agar chaahe…to is ek pathe ke sahaare apki imaarat ke har ek inth ko hilaa kar rakh de.'), array(
      8956 , 'Mohabbat bhi zindagi ki tarha hoti hai…har mod aasaan nahin hota, har mod par khushi nahin milti…par jab hum zindagi ka saath nahin chhodte…phir mohabbat ka saath kyon chhodein?'), array(
      6149 , 'Mogambo…khush hua!'), array(
      15113 , 'Kaaton ko murjhaane ka khauf nahin hota.'), array(
      15113 , 'Humara Hindustan koi tumhara dil nahin, ke laundi jiski mallika bane!'), array(
      15113 , 'Hum apne bete ke dhadakte hue dil ke liye Hindustan ki taqdeer nahin badal sakte!'), array(
      15113 , 'Taqdeerein badal jaati hain…zamaana badal jaata hai…mulkon ki tareekh badal jaati hain…shahenshah badal jaatein hain. Magar is badalti hui duniya mein mohabbat jis insaan ka daaman thaam leti hai…woh insaan nahin badalta.'), array(
      15113 , 'Kya Parwardigaar-e-alam se aapne mujhe is hi liye maanga tha ke zindagi mujhe mile aur uski maalik aap? Saansein meri ho aur dil ki dhadkanon pe qabza aap ka rahe? Zil-e-Ilaahi, kya meri zindagi aap ki duaaon ka karza hai jo mujhe apni aansoon se ada karna parega?'), array(
      15113 , 'Meri aankhon se mere khwaab na chheeniye, Shahzaade…main mar jaaoongi!'), array(
      15113 , 'Mohabbat jo darrti, woh mohabbat nahin hai…ayaashi, gunaah hai!'), array(
      15113 , 'Kabhi kabhi taare khud tooth kar zameen par ghir aate hain…maghroor shahzaadon ke liye ek jhuki hui nazar hi kaafi hoti hai.'), array(
      15113 , 'Taaj un saron par nahin rehta jin ke kareeb khauf aa jaaye.'), array(
      15113 , 'Mohabbat hum ne maana zindagi barbaad karti hai / Yeh kya kam hai ke marjaane pe duniya yaad karti hai / Kisi ke ishq mein duniya luta kar hum bhi dhekenge.'), array(
      15113 , 'Mohabbat karne waalon ka hai bus itna hi afsaana / Tadapna chup ke chup ke aah bharna, ghut ke mar jaana / Kisi din yeh tamaasha muskuraa kar hum bhi dhekenge.'), array(
      15113 , 'Anarkali yeh zindagi se bewafaai kar sakti hai…aap se nahin!'), array(
      8287 , 'My name is Khan…and I am not a terrorist.'), array(
      5036 , 'Babuji, aisi English aave that I can leave Angrej behind. You see, sar, I can taak English, I can waak English, I can laaf English, because English is a very phunny language.'), array(
      16996 , 'Itni shiddat se maine tumhe paane ki koshish ki hai / Ke har zarre ne mujhe tumse milaane ki saazish ki hai.'), array(
      16996 , 'Kehte hain agar kisi cheez ko dil se chaaho to puri kaayanat use tumse milane ki koshish mein lag jaati hai.'), array(
      16996 , 'Humaari filmon ki tarha, humari zindagi mein bhi end mein sab theek ho jaata hai. Happys endings. Aur agar…aur agar theek na ho to woh "the end" nahin hai, doston…picture abhi baaqi hai!'), array(
      16996 , 'Yanna Rascala! Mind it!'), array(
      16996 , 'You bad cat! You fat cat! Rascala cat! Naughty pussy! Naughty pussy! Naughty pussy!'), array(
      16996 , 'Udi baba!'), array(
      16996 , 'What the fish!'), array(
      13834 , 'Zindagi ho to smuggler jaisi…saari duniya raakh ki tarha neeche…aur khud dhuein ki tarha upar.'), array(
      13834 , 'Main un cheezon ki smuggling karta hoon jinki ijaazat sarkar nahin deti…un cheezon ki nahin jinki ijaazat zameer nahin deta.'), array(
      13834 , 'Himmat bataaee nahin…dikhaaee jaati hai.'), array(
      13834 , 'Raaste ki parva karoonga to...manzil buraa maan jayegi.'), array(
      4019 , 'Ladka hai!!! Ladka hai, sir! Lekin Hindustani nahin hai!'), array(
      4019 , 'Sach?! Sach to aap jaan na hi nahin chahte! Kyon ki jo sach sun na chahte hain, voh apne jeb mein pistol aur dil mein nafrat le kar nahin aate, sir! Bahut bada kaleja chahiye, sir…bahut bada kaleja chahiye sach ko sun ne ke liye!'), array(
      4019 , 'Agar Ganga ki hifaazat karna pyaar hai…to hai! Hai! Hai! Pyaar hai!'), array(
      10459 , 'Always say: Hum hain raahi pyaar ke…phir milenge…chalte chalte!'), array(
      18462 , 'Rishte mein to hum tumhaare baap hotein hain…naam hai Shahenshah!'), array(
      7763 , 'Arey, bhai, shaarabi ko "sharaabi" nahin to kya "pujaari" kahoge? Arey! Yeh to shair ho gaya!'), array(
      58541 , 'Kitne aadmi the?'), array(
      58541 , 'Chal, Dhanno! Aaj teri Basanti ki ijjat ka sawaal hai!'), array(
      58541 , 'Arey O, Sambha!'), array(
      58541 , 'Nahin!!! Basanti, in kutton ke saamne mat naachna!'), array(
      58541 , 'Yahaan se pachaas pachaas kos dur gaon mein, jab bacha raat ko rota hai, to maa kehti hai, "Beta, soja…soja nahin to Gabbar Singh aa jayega!"'), array(
      58541 , 'Chey goli…aur aadmi teen. Bahut na-insaafi hai yeh!'), array(
      58541 , 'Tera kya hoga, Kaalia?'), array(
      58541 , 'Jo darr gaya…samjho mar gaya!'), array(
      58541 , 'Yeh badi dukh bhari kahaani hai! Is istory mein emotion hai, drrrama hai, trrragedy hai! Yeh Basanti hai na? Is se mera lagan hone wala tha, Chacha!'), array(
      58541 , 'When I death, police coming! Police coming, buddhiya going jail! In jail, buddhiya chakki peesing…and peesing…and peesing!'), array(
      58541 , 'Ae, gaon waalon! Suna tum ne, bhaiyyon?!? Mausi bhi tayaar hai…basanti bhi tayaar hai! Is liye marna cancel!'), array(
      58541 , 'Wohi kar raha hoon, bhaiyya, jo Majnu ne Laila ke liye kiya tha…Ranjha ne Heer ke liye kiya tha…Romeo ne Juliet ke liye kiya tha: SUICIDE!'), array(
      58541 , 'Dekho…mujhe befajool baat karne ki aadat to hai nahin…chalna hai to bolo chalna hai.'), array(
      9932 , 'Main aur meri tanhaai aksar yeh baatein kartein hain: tum hoti to kaisa hota? Tum yeh kehti…tum woh kehti…tum is baat pe hairaan hoti…tum us baat pe kitni hasti…tum hoti to aisa hota…tum hoti to waisa hota. Main aur meri tanhaai aksar yeh baatein kartein hain...'), array(
      9932 , 'Tu kisi aur ki raaton ka haseen chand sahi / Meri duniya ke har rang mein shaamil tu hai.'), array(
      6232 , 'So let\'s play…"Who Wants to be a Millunnaire!"'), array(
      14099 , 'Mujhse paanch minute na mil kar aapne apna paanch lakh ka nuksaan kiya hai.'), array(
      16857 , 'Main…qaidi number saat sau chayaasi…jail ki salaakhon se bahar dekhta hoon.'), array(
      16857 , 'Sarhad paar ek aisa shaks hai…jo aap ke liye apni jaan bhi dedega.'), array(
      16857 , 'Us ek nadaani ke sahaare to main apni puri zindagi jeene wala hoon.'), array(
      3487 , 'Ek baar jo main ne commitment kar li…phir main apne aap ki bhi nahin sunta!'), array(
      17379 , 'Ek machad…ek machad saala aadmi ko hijda banaa deta hai.'), array(
      8556 , 'Jab tak baithne ko na kaha jaaye sharaafat se khade raho. Yeh police station hai, tumhare baap ka ghar nahin.'), array(
      3946 , 'Puedo entrar, por favor?'), array(
      3946 , 'Arjun, my bwoy, tum is the uniform mein the mantal lag rahe ho.'), array(
										       3946 , 'Mujhe afsos karna nahin aata.')
      );

hlog($quotes);
$i = 1;
foreach ($quotes as $data) {
  list($film_id, $quote) = $data;
  $sql = sprintf("insert ignore into quotes (film_id, quote) "
                 ."values (%d, '%s')",
		 $film_id,
                 tr($quote));
  
  hlog($i++.' [] '.$sql);
  //sleep(1);
  //$result = mysql_query($sql);
}
