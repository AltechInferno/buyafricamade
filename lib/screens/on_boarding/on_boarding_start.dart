
import 'package:active_ecommerce_flutter/constants/colors.dart';
import 'package:active_ecommerce_flutter/screens/login.dart';
import 'package:active_ecommerce_flutter/screens/main.dart';
import 'package:active_ecommerce_flutter/screens/on_boarding/on_boarding_page.dart';
import 'package:active_ecommerce_flutter/widgets/global/BAMElevatedButton.dart';
import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:shared_preferences/shared_preferences.dart';

class OnBoardingStart extends StatefulWidget {
  OnBoardingStart({Key key}) : super(key: key);

  @override
  State<OnBoardingStart> createState() => _OnBoardingStartState();
}

class _OnBoardingStartState extends State<OnBoardingStart> {
  int currentPage = 0;

  final PageController _pageController =
      PageController(initialPage: 0, keepPage: false);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Column(
        children: [
          Expanded(
            flex: 2,
            child: PageView(
              onPageChanged: (newPageNumber) {
                setState(() {
                  currentPage = newPageNumber;
                });
              },
              controller: _pageController,
              children: const [
                OnBoardingPage(
                    img: "Thriftshop-rafiki1",
                    text: """Find Millions Of African Made 
Goods At Your Fingertips"""),
                OnBoardingPage(
                    img: "Discount-rafiki1",
                    text: """Enjoy Great Discounts On
Your Favorite Goods"""),
                OnBoardingPage(
                    img: "Dropshippingmodel-rafiki1",
                    text: """Track Your Purchase Till it 
Reaches Your Doorstep""")
              ],
            ),
          ),
          Expanded(
            flex: 1,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Row(
                  children: List.generate(3, (index) => getIndicator(index)),
                  mainAxisAlignment: MainAxisAlignment.center,
                ),
                SizedBox(
                  height: 70.h,
                ),
                BAMElevatedButton(
                    text: "Next",
                    onPress: () async{
                      if (currentPage == 2) {
                        final prefs =  await SharedPreferences.getInstance();
                        prefs.setBool("isDoneOnBoarding", true);
                        Navigator.pushReplacement(
                            context,
                            MaterialPageRoute(
                                builder: (context) => Main()));
                      } else {
                        _pageController.animateToPage(currentPage + 1,
                            duration: Duration(milliseconds: 300),
                            curve: Curves.linear);
                      }
                    }),
              ],
            ),
          ),
        ],
      ),
    );
  }

  AnimatedContainer getIndicator(int pageNo) {
    return AnimatedContainer(
      duration: Duration(
        milliseconds: 300,
      ),
      width: 15.w,
      height: 15.h,
      margin: EdgeInsets.symmetric(horizontal: 6.w),
      decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: (currentPage == pageNo ? primaryColor : Colors.grey)),
    );
  }
}
//
// Row(
// mainAxisAlignment: MainAxisAlignment.center,
// children: List.generate(3, (index) => getIndicator(index)),
// )
