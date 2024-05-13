import 'dart:math';

import 'package:active_ecommerce_flutter/app_config.dart';
import 'package:active_ecommerce_flutter/my_theme.dart';
import 'package:active_ecommerce_flutter/widgets/global/BAMElevatedButton.dart';
import 'package:active_ecommerce_flutter/widgets/global/BAMOutlinedInput.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:active_ecommerce_flutter/custom/input_decorations.dart';
import 'package:active_ecommerce_flutter/custom/intl_phone_input.dart';
import 'package:intl_phone_number_input/intl_phone_number_input.dart';
import 'package:active_ecommerce_flutter/screens/otp.dart';
import 'package:active_ecommerce_flutter/screens/login.dart';
import 'package:active_ecommerce_flutter/custom/toast_component.dart';
import 'package:toast/toast.dart';
import 'package:active_ecommerce_flutter/repositories/auth_repository.dart';
import 'package:active_ecommerce_flutter/helpers/shared_value_helper.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';


class Registration extends StatefulWidget {
  @override
  _RegistrationState createState() => _RegistrationState();
}

class _RegistrationState extends State<Registration> {
  String _register_by = "email"; //phone or email
  String initialCountry = 'US';
  PhoneNumber phoneCode = PhoneNumber(isoCode: 'US', dialCode: "+1");

  String _phone = "";

  //controllers
  TextEditingController _nameController = TextEditingController();
  TextEditingController _emailController = TextEditingController();
  TextEditingController _phoneNumberController = TextEditingController();
  TextEditingController _passwordController = TextEditingController();
  TextEditingController _passwordConfirmController = TextEditingController();

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

  onPressSignUp() async {
    var name = _nameController.text.toString();
    var email = _emailController.text.toString();
    var password = _passwordController.text.toString();
    var password_confirm = _passwordConfirmController.text.toString();

    if (name == "") {
      ToastComponent.showDialog(AppLocalizations.of(context).registration_screen_name_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    } else if (_register_by == 'email' && email == "") {
      ToastComponent.showDialog(AppLocalizations.of(context).registration_screen_email_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    } else if (_register_by == 'phone' && _phone == "") {
      ToastComponent.showDialog(AppLocalizations.of(context).registration_screen_phone_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    } else if (password == "") {
      ToastComponent.showDialog(AppLocalizations.of(context).registration_screen_password_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    } else if (password_confirm == "") {
      ToastComponent.showDialog(AppLocalizations.of(context).registration_screen_password_confirm_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    } else if (password.length < 6) {
      ToastComponent.showDialog(
          AppLocalizations.of(context).registration_screen_password_length_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    } else if (password != password_confirm) {
      ToastComponent.showDialog(AppLocalizations.of(context).registration_screen_password_match_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    }

    var signupResponse = await AuthRepository().getSignupResponse(
        name,
        _register_by == 'email' ? email : _phone,
        password,
        password_confirm,
        _register_by);

    if (signupResponse.result == false) {
      ToastComponent.showDialog(signupResponse.message, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
    } else {
      ToastComponent.showDialog(signupResponse.message, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      Navigator.push(context, MaterialPageRoute(builder: (context) {
        return Otp(
          verify_by: _register_by,
          user_id: signupResponse.user_id,
        );
      }));
    }
  }


  final GlobalKey<FormState> _formKey = GlobalKey();
  bool _isPasswordVisible = false;
  bool _isConfirmPasswordVisible = false;
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
                  top: _screen_height * 0.11607,
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
                                    },
                                    child: Ink(
                                      child: Text(
                                        "Sign Up",
                                        style: TextStyle(
                                            fontWeight: FontWeight.w700,
                                            height: 1.5,
                                            fontSize: 16.0),
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
                            ),
                            IntrinsicWidth(
                              child: Column(
                                children: [
                                  InkWell(
                                    onTap: () {
                                      Navigator.pushReplacement(context,
                                          MaterialPageRoute(builder: (context) {
                                            return Login();
                                          }));

                                    },
                                    child: Ink(
                                      child: Text(
                                        "Login",
                                        style: TextStyle(
                                          color: Color(0xFFC4C4C4),

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
                                ],
                              ),
                            )
                          ],
                        ),
                        SizedBox(height: _screen_height * 20 / 896),
                        BAMOutlinedInput(
                          hintText: "Email",
                          controller: _emailController,
                        ),
                        SizedBox(height: _screen_height * 25 / 896),
                        BAMOutlinedInput(
                          hintText: " Name",
                          controller: _nameController,
                        ),

                        SizedBox(height:_screen_height * 25 / 896),
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
                        SizedBox(height: _screen_height * 25 / 896),

                        BAMOutlinedInput(
                          hintText: "Retype Password",
                          enableSuggestions: false,
                          autocorrect: false,
                          obscureText: !_isConfirmPasswordVisible,
                          controller: _passwordConfirmController,
                          trailing: IconButton(
                            onPressed: () {
                              setState(() {
                                _isConfirmPasswordVisible= !_isConfirmPasswordVisible;
                              });
                            },
                            icon: _isConfirmPasswordVisible
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
                                  onPressSignUp();
                                }, text: "Sign Up")),
                        SizedBox(
                          height: 20,
                        ),
                      ],
                    ),
                  ),
                ),
                Expanded(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      //TODO:Modify on allow google or facebook login
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
                      // SizedBox(height: _screen_height * 15 / 896),
                      // Row(
                      //   mainAxisAlignment: MainAxisAlignment.center,
                      //   children: [
                      //     Text(
                      //       "By clicking on Signup, you agree to our ",
                      //       style: TextStyle(
                      //         fontWeight: FontWeight.w700,
                      //         fontSize: 11.0,
                      //       ),
                      //     ),
                      //     Text(
                      //       "Terms and conditions",
                      //       style: TextStyle(
                      //           fontWeight: FontWeight.w700,
                      //           fontSize: 11.0,
                      //           color:  Color(0xFFEF4533)),
                      //     )
                      //   ],
                      // ),
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

