import 'dart:io';

import 'package:active_ecommerce_flutter/custom/CommonFunctoins.dart';
import 'package:active_ecommerce_flutter/my_theme.dart';
import 'package:active_ecommerce_flutter/screens/cart.dart';
import 'package:active_ecommerce_flutter/screens/category_list.dart';
import 'package:active_ecommerce_flutter/screens/home.dart';
import 'package:active_ecommerce_flutter/screens/login.dart';
import 'package:active_ecommerce_flutter/screens/profile.dart';
import 'package:active_ecommerce_flutter/screens/filter.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'dart:ui';
import 'package:flutter/services.dart';
import 'package:active_ecommerce_flutter/helpers/shared_value_helper.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'package:flutter_svg/flutter_svg.dart';

class Main extends StatefulWidget {
  Main({Key key, go_back = true}) : super(key: key);

  bool go_back;

  @override
  _MainState createState() => _MainState();
}

class _MainState extends State<Main> {
  int _currentIndex = 0;
  var _children = [
    Home(),
    CategoryList(
      is_base_category: true,
    ),
    Home(),
    Cart(has_bottomnav: true),
    Profile()
  ];

  void onTapped(int i) {

    if (!is_logged_in.$ && (i == 4 || i == 3)) {
      Navigator.push(context, MaterialPageRoute(builder: (context) => Login()));
      return;
    }
    setState(() {
      _currentIndex = i;
    });
    print("i$i");
  }

  void initState() {
    // TODO: implement initState
    //re appear statusbar in case it was not there in the previous page
    SystemChrome.setEnabledSystemUIOverlays(
        [SystemUiOverlay.top, SystemUiOverlay.bottom]);
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: () async {
        print("_currentIndex");
        if (_currentIndex != 0) {
          setState(() {
            _currentIndex = 0;
          });
          return false;
        } else {
          CommonFunctions(context).appExitDialog();
        }
        return widget.go_back;
      },
      child: Directionality(
        textDirection:
            app_language_rtl.$ ? TextDirection.rtl : TextDirection.ltr,
        child: Scaffold(
          extendBody: true,
          body: _children[_currentIndex],
          floatingActionButtonLocation:
              FloatingActionButtonLocation.centerDocked,
          //specify the location of the FAB
          floatingActionButton: Visibility(
            visible: MediaQuery.of(context).viewInsets.bottom ==
                0.0, // if the kyeboard is open then hide, else show
            child: FloatingActionButton(
              backgroundColor: MyTheme.accent_color,
              onPressed: () {},
              tooltip: "start FAB",
              child: Container(
                  margin: EdgeInsets.all(0.0),
                  child: IconButton(
                      icon: new Image.asset('assets/square_logo.png'),
                      tooltip: 'Action',
                      onPressed: () {
                        Navigator.push(context,
                            MaterialPageRoute(builder: (context) {
                          return Filter(
                            selected_filter: "sellers",
                          );
                        }));
                      })),
              elevation: 0.0,
            ),
          ),
          bottomNavigationBar: Container(
            margin: EdgeInsets.only(
                left: MediaQuery.of(context).size.width * 0.056,
                right: MediaQuery.of(context).size.width * 0.056,
                bottom: 20),
            decoration: BoxDecoration(
              boxShadow: <BoxShadow>[
                BoxShadow(
                  color: Colors.black.withOpacity(0.14),
                  offset: Offset(0.0, 14.0),
                  blurRadius: 56.0,
                ),
              ],
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(69),
              child: BottomNavigationBar(
                elevation: 0.0,
                showSelectedLabels: false,
                showUnselectedLabels: false,
                type: BottomNavigationBarType.fixed,
                onTap: onTapped,


                currentIndex: _currentIndex,
                backgroundColor: Colors.white,
                fixedColor: Theme.of(context).accentColor,
                unselectedItemColor: Color.fromRGBO(153, 153, 153, 1),
                selectedFontSize: 0,
                items: [
                  BottomNavigationBarItem(
                    // icon: Image.asset(
                    //   "assets/home.png",
                    //   color: _currentIndex == 0
                    //       ? Theme.of(context).accentColor
                    //       : Color.fromRGBO(153, 153, 153, 1),
                    //   height: 20,
                    // ),
                      icon: Padding(
                        padding: const EdgeInsets.symmetric(vertical: 20.0),
                        child: SvgPicture.asset(
                          "assets/home_new.svg",
                          color: _currentIndex == 0 ? Color(0xFFEF4533) : null,
                        ),
                      ),
                      title: Text(
                        AppLocalizations.of(context)
                            .main_screen_bottom_navigation_home,
                        style: TextStyle(fontSize: 0),
                      )),
                  BottomNavigationBarItem(
                    // icon: Image.asset(
                    //   "assets/categories.png",
                    //   color: _currentIndex == 1
                    //       ? Theme.of(context).accentColor
                    //       : Color.fromRGBO(153, 153, 153, 1),
                    //   height: 20,
                    // ),
                      icon: Padding(
                        padding: const EdgeInsets.symmetric(vertical: 20.0),
                        child: SvgPicture.asset(
                          "assets/apps_new.svg",
                          color: _currentIndex == 1 ? Color(0xFFEF4533) : null,

                        ),
                      ),
                      title: Text(
                        AppLocalizations.of(context)
                            .main_screen_bottom_navigation_categories,
                        style: TextStyle(fontSize: 0),
                      )),
                  BottomNavigationBarItem(
                    icon: Icon(
                      Icons.circle,
                      color: Colors.transparent,
                    ),
                    title: Text(""),
                  ),
                  BottomNavigationBarItem(
                    // icon: Image.asset(
                    //   "assets/cart.png",
                    //   color: _currentIndex == 3
                    //       ? Theme.of(context).accentColor
                    //       : Color.fromRGBO(153, 153, 153, 1),
                    //   height: 20,
                    // ),
                      icon: Padding(
                        padding: const EdgeInsets.symmetric(vertical: 20.0),
                        child: SvgPicture.asset(
                          "assets/cart_new.svg",
                          color: _currentIndex == 3 ? Color(0xFFEF4533) : null,

                        ),
                      ),
                      title: Text(
                        AppLocalizations.of(context)
                            .main_screen_bottom_navigation_cart,
                        style: TextStyle(fontSize: 0),

                      )),
                  BottomNavigationBarItem(
                    // icon: Image.asset(
                    //   "assets/profile.png",
                    //   color: _currentIndex == 4
                    //       ? Theme.of(context).accentColor
                    //       : Color.fromRGBO(153, 153, 153, 1),
                    //   height: 20,
                    // ),
                    icon: Padding(
                      padding: const EdgeInsets.symmetric(vertical: 20.0),
                      child: SvgPicture.asset(
                        "assets/user_new.svg",
                        color: _currentIndex == 4 ? Color(0xFFEF4533) : null,

                      ),
                    ),
                    title: Text(
                      AppLocalizations.of(context)
                          .main_screen_bottom_navigation_profile,
                      style: TextStyle(fontSize: 0),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }


}
