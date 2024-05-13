import 'dart:math';

import 'package:active_ecommerce_flutter/app_config.dart';
import 'package:active_ecommerce_flutter/my_theme.dart';
import 'package:active_ecommerce_flutter/other_config.dart';
import 'package:active_ecommerce_flutter/social_config.dart';
import 'package:active_ecommerce_flutter/widgets/global/BAMElevatedButton.dart';
import 'package:active_ecommerce_flutter/widgets/global/BAMOutlinedInput.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:active_ecommerce_flutter/custom/input_decorations.dart';
import 'package:active_ecommerce_flutter/custom/intl_phone_input.dart';
import 'package:flutter_facebook_auth/flutter_facebook_auth.dart';
import 'package:intl_phone_number_input/intl_phone_number_input.dart';
import 'package:active_ecommerce_flutter/screens/registration.dart';
import 'package:active_ecommerce_flutter/screens/main.dart';
import 'package:active_ecommerce_flutter/screens/password_forget.dart';
import 'package:active_ecommerce_flutter/custom/toast_component.dart';
import 'package:toast/toast.dart';
import 'package:active_ecommerce_flutter/repositories/auth_repository.dart';
import 'package:active_ecommerce_flutter/helpers/auth_helper.dart';
import 'package:google_sign_in/google_sign_in.dart';

import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:active_ecommerce_flutter/helpers/shared_value_helper.dart';
import 'package:active_ecommerce_flutter/repositories/profile_repository.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'package:twitter_login/twitter_login.dart';


class Login extends StatefulWidget {
  @override
  _LoginState createState() => _LoginState();
}

class _LoginState extends State<Login> {
  String _login_by = "email"; //phone or email
  String initialCountry = 'US';
  PhoneNumber phoneCode = PhoneNumber(isoCode: 'US', dialCode: "+1");
  String _phone = "";

  //controllers
  TextEditingController _phoneNumberController = TextEditingController();
  TextEditingController _emailController = TextEditingController();
  TextEditingController _passwordController = TextEditingController();

  @override
  void initState() {
    //on Splash Screen hide statusbar
    SystemChrome.setEnabledSystemUIOverlays([SystemUiOverlay.bottom]);
    super.initState();
  }

  @override
  void dispose() {
    //before going to other screen show statusbar
    SystemChrome.setEnabledSystemUIOverlays(
        [SystemUiOverlay.top, SystemUiOverlay.bottom]);
    super.dispose();
  }

  onPressedLogin() async {
    var email = _emailController.text.toString();
    var password = _passwordController.text.toString();

    if (_login_by == 'email' && email == "") {
      ToastComponent.showDialog(AppLocalizations.of(context).login_screen_email_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    } else if (_login_by == 'phone' && _phone == "") {
      ToastComponent.showDialog(AppLocalizations.of(context).login_screen_phone_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    } else if (password == "") {
      ToastComponent.showDialog(AppLocalizations.of(context).login_screen_password_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    }

    var loginResponse = await AuthRepository()
        .getLoginResponse(_login_by == 'email' ? email : _phone, password);
    if (loginResponse.result == false) {
      ToastComponent.showDialog(loginResponse.message, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
    } else {

      ToastComponent.showDialog(loginResponse.message, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      AuthHelper().setUserData(loginResponse);
      // push notification starts
      if (OtherConfig.USE_PUSH_NOTIFICATION) {
        final FirebaseMessaging _fcm = FirebaseMessaging.instance;

        await _fcm.requestPermission(
          alert: true,
          announcement: false,
          badge: true,
          carPlay: false,
          criticalAlert: false,
          provisional: false,
          sound: true,
        );

        String fcmToken = await _fcm.getToken();

        if (fcmToken != null) {
          print("--fcm token--");
          print(fcmToken);
          if (is_logged_in.$ == true) {
            // update device token
            var deviceTokenUpdateResponse = await ProfileRepository()
                .getDeviceTokenUpdateResponse(fcmToken);
          }
        }
      }

      //push norification ends

      Navigator.push(context, MaterialPageRoute(builder: (context) {
        return Main();
      }));
    }
  }

  onPressedFacebookLogin() async {
    final facebookLogin =await FacebookAuth.instance.login(loginBehavior: LoginBehavior.webOnly);

    if (facebookLogin.status == LoginStatus.success) {

      // get the user data
      // by default we get the userId, email,name and picture
      final userData = await FacebookAuth.instance.getUserData();
      var loginResponse = await AuthRepository().getSocialLoginResponse("facebook",
          userData['name'].toString(), userData['email'].toString(), userData['id'].toString(),access_token: facebookLogin.accessToken.token);
      print("..........................${loginResponse.toString()}");
      if (loginResponse.result == false) {
        ToastComponent.showDialog(loginResponse.message, context,
            gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      } else {
        ToastComponent.showDialog(loginResponse.message, context,
            gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
        AuthHelper().setUserData(loginResponse);
        Navigator.push(context, MaterialPageRoute(builder: (context) {
          return Main();
        }));
        FacebookAuth.instance.logOut();
      }
      // final userData = await FacebookAuth.instance.getUserData(fields: "email,birthday,friends,gender,link");

    } else {
      print("....Facebook auth Failed.........");
      print(facebookLogin.status);
      print(facebookLogin.message);
    }



  }

  onPressedGoogleLogin() async {
    try {
      final GoogleSignInAccount googleUser = await GoogleSignIn().signIn();


      print(googleUser.toString());

      GoogleSignInAuthentication googleSignInAuthentication =
      await googleUser.authentication;
      String accessToken = googleSignInAuthentication.accessToken;


      var loginResponse = await AuthRepository().getSocialLoginResponse("google",
          googleUser.displayName, googleUser.email, googleUser.id,access_token: accessToken);

      if (loginResponse.result == false) {
        ToastComponent.showDialog(loginResponse.message, context,
            gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      } else {
        ToastComponent.showDialog(loginResponse.message, context,
            gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
        AuthHelper().setUserData(loginResponse);
        Navigator.push(context, MaterialPageRoute(builder: (context) {
          return Main();
        }));
      }
      GoogleSignIn().disconnect();
    } on Exception catch (e) {
      print("error is ....... $e");
      // TODO
    }



  }

  onPressedTwitterLogin() async {
    try {

      final twitterLogin = new TwitterLogin(
          apiKey: SocialConfig().twitter_consumer_key,
          apiSecretKey:SocialConfig().twitter_consumer_secret,
          redirectURI: 'activeecommerceflutterapp://'

      );
      // Trigger the sign-in flow
      final authResult = await twitterLogin.login();

      var loginResponse = await AuthRepository().getSocialLoginResponse("twitter",
          authResult.user.name, authResult.user.email, authResult.user.id.toString(),access_token: authResult.authToken);
      print(loginResponse);
      if (loginResponse.result == false) {
        ToastComponent.showDialog(loginResponse.message, context,
            gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      } else {
        ToastComponent.showDialog(loginResponse.message, context,
            gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
        AuthHelper().setUserData(loginResponse);
        Navigator.push(context, MaterialPageRoute(builder: (context) {
          return Main();
        }));
      }
    } on Exception catch (e) {
      print("error is ....... $e");
      // TODO
    }



  }

  final GlobalKey<FormState> _formKey = GlobalKey();
  bool _isPasswordVisible = false;
  @override
  Widget build(BuildContext context) {
    final _screen_height = MediaQuery.of(context).size.height;
    final _screen_width = MediaQuery.of(context).size.width;
    return Directionality(
      textDirection: app_language_rtl.$ ? TextDirection.rtl : TextDirection.ltr,
      child: Scaffold(
        resizeToAvoidBottomInset: false,
        body: Stack(
          children: [
            Stack(
              alignment: Alignment.center,
              children: [
                Image.asset("assets/curved_rect.png", height: _screen_height * 0.3747, width: double.maxFinite, fit: BoxFit.fill,),
                Positioned(
                  child: Align(
                      child: Image.asset("assets/buy-africa-made-logo.png")),
                  top:  _screen_height * 0.11607,
                )
              ],
            ),
            Column(
              children: [
                SizedBox(
                  height: _screen_height * 0.22321,
                ),
                Container(
                  padding: EdgeInsets.only(right: 12.0, left: 12.0, top: _screen_height * 0.04464285714),
                  margin: EdgeInsets.symmetric(horizontal: 30.0),
                  decoration: BoxDecoration(
                      boxShadow: <BoxShadow>[
                        BoxShadow(
                          color: Color(0xFFFFFFFF).withOpacity(0.1),
                          offset: Offset(0.0, 4.0),
                          blurRadius: 20.0,
                        ),
                      ],
                      borderRadius: BorderRadius.circular(30),
                      color: Colors.white,
                      border: Border.all(color: Colors.transparent)),
                  child: Form(
                    key: _formKey,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            IntrinsicWidth(
                              child: Column(
                                children: [
                                  InkWell(
                                    onTap: (){
                                      Navigator.pushReplacement(context,
                                          MaterialPageRoute(builder: (context) {
                                            return Registration();
                                          }));
                                    },
                                    child: Ink(
                                      child: Text(
                                        "Sign Up",
                                        style: TextStyle(
                                            color: Color(0xFFC4C4C4),
                                            fontWeight: FontWeight.w700,
                                            height: 1.5,
                                            fontSize: 16.0),
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            IntrinsicWidth(
                              child: Column(
                                children: [
                                  Ink(
                                    child: InkWell(
                                      onTap: () {
                                        setState(() {
                                          // _authMode = AuthMode.Login;
                                        });
                                      },
                                      child: Text(
                                        "Login",
                                        style: TextStyle(
                                          fontWeight: FontWeight.w700,
                                          height: 1.5,
                                          fontSize: 16.0,
                                          // color: _authMode == AuthMode.SignUp
                                          //     ? Color(0xFFC4C4C4)
                                          //     : Colors.black,
                                        ),
                                      ),
                                    ),
                                  ),
                                  Container(
                                      height: 4.0,
                                      decoration: BoxDecoration(
                                        color: Color(0xFFEF4533),
                                        borderRadius: BorderRadius.all(
                                            const Radius.circular(3.0)),
                                      )),
                                ],
                              ),
                            )
                          ],
                        ),
                        SizedBox(height:_screen_height * 20 / 896),
                        BAMOutlinedInput(
                          hintText: "Email",
                          controller: _emailController,
                        ),
                        SizedBox(height:  _screen_height * 25 / 896),
                        BAMOutlinedInput(
                          hintText: "Password",
                          enableSuggestions: false,
                          autocorrect: false,
                          obscureText: !_isPasswordVisible,
                          controller: _passwordController,
                          trailing: IconButton(
                            onPressed: () {
                              setState(() {
                                _isPasswordVisible = !_isPasswordVisible;
                              });
                            },
                            icon: _isPasswordVisible
                                ? Icon(
                              Icons.visibility,
                              color: Colors.grey,
                            )
                                : Icon(
                              Icons.visibility_off,
                              color: Colors.grey,
                            ),
                          ),
                        ),
                        SizedBox(
                          height: _screen_height * 30 / 896,
                        ),
                        Center(
                            child: BAMElevatedButton(
                                onPress: () {
                                  onPressedLogin();
                                }, text: "Login")),
                        SizedBox(
                          height:  _screen_height * 20 / 896,
                        ),
                      ],
                    ),
                  ),
                ),
                Expanded(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      // //TODO:Modify on allow google or facebook login
                      // Row(
                      //   mainAxisAlignment: MainAxisAlignment.center,
                      //   children: [
                      //     InkWell(onTap: (){}, child: Ink(child: Image.asset("assets/twitter-logo.png"))),
                      //     SizedBox(width: 10,),
                      //     InkWell(onTap: (){},child: Ink(child: Image.asset("assets/facebook-logo.png"))),
                      //     SizedBox(width: 10,),
                      //     InkWell(onTap: (){},child: Ink(child: Image.asset("assets/google-logo.png"))),
                      //   ],
                      // ),
                      // SizedBox(height:  _screen_height * 30 / 896),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            "Forgot Password? ",
                            style: TextStyle(
                              fontWeight: FontWeight.w700,
                              fontSize: 12.0,
                            ),
                          ),
                          InkWell(
                            onTap: (){
                              Navigator.push(context,
                                  MaterialPageRoute(builder: (context) {
                                    return PasswordForget();
                                  }));
                            },
                            child: Ink(
                              child: Text(
                                "Click Here ",
                                style: TextStyle(
                                    fontWeight: FontWeight.w700,
                                    fontSize: 12.0,
                                    color: Color(0xFFEF4533)),
                              ),
                            ),
                          )
                        ],
                      )
                    ],
                  ),
                )
              ],
            )
          ],
        ),
      ),
    );
  }
}


// class BAMOutlinedInput extends StatelessWidget {
//   BAMOutlinedInput(
//       {Key key,
//         @required this.hintText,
//         this.controller,
//         this.trailing,
//         this.obscureText = false, this.enableSuggestions = true, this.autocorrect= true})
//       : super(key: key);
//   final String hintText;
//   // String Function(String) validator;
//   TextEditingController controller;
//   final Widget trailing;
//   bool obscureText;
//   bool enableSuggestions;
//   bool autocorrect;
//   @override
//   Widget build(BuildContext context) {
//     final _screen_height = MediaQuery.of(context).size.height;
//     final _screen_width = MediaQuery.of(context).size.width;
//     return Container(
//       decoration: BoxDecoration(
//         borderRadius: BorderRadius.circular(15.0),
//         color: Colors.white,
//         boxShadow: <BoxShadow>[
//           BoxShadow(
//             color: Color(0xFF000000).withOpacity(0.05),
//             offset: Offset(0.0, 4.0),
//             blurRadius: 10.0,
//           ),
//         ],
//       ),
//       child: TextFormField(
//         autofocus: false,
//         enableSuggestions: enableSuggestions,
//         autocorrect: autocorrect,
//         obscureText: obscureText,
//         controller: controller,
//         autovalidateMode: AutovalidateMode.onUserInteraction,
//         // validator: validator,
//         decoration: InputDecoration(
//           suffixIcon: trailing,
//           contentPadding: EdgeInsets.symmetric(vertical: _screen_height * 15 / 896, horizontal: _screen_height * 12 / 896),
//           hintText: hintText,
//           hintStyle: TextStyle(
//             color: Color(0xFFD8D8D8),
//             fontWeight: FontWeight.w400,
//           ),
//           border: OutlineInputBorder(
//               borderSide: BorderSide.none,
//               borderRadius: BorderRadius.circular(15)),
//         ),
//       ),
//     );
//   }
// }

