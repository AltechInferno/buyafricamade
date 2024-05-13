import 'package:active_ecommerce_flutter/constants/spacing.dart';
import 'package:active_ecommerce_flutter/my_theme.dart';
import 'package:active_ecommerce_flutter/utils/ui.dart';
import 'package:active_ecommerce_flutter/widgets/global/BAMElevatedButton.dart';
import 'package:active_ecommerce_flutter/widgets/global/BAMOutlinedInput.dart';
import 'package:active_ecommerce_flutter/widgets/global/BAMTextButton.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:active_ecommerce_flutter/custom/input_decorations.dart';
import 'package:active_ecommerce_flutter/custom/toast_component.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:toast/toast.dart';
import 'package:active_ecommerce_flutter/repositories/auth_repository.dart';
import 'package:active_ecommerce_flutter/screens/login.dart';
import 'package:active_ecommerce_flutter/helpers/shared_value_helper.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';

class PasswordOtp extends StatefulWidget {
  PasswordOtp({Key key, this.verify_by = "email", this.email_or_code})
      : super(key: key);
  final String verify_by;
  final String email_or_code;

  @override
  _PasswordOtpState createState() => _PasswordOtpState();
}

class _PasswordOtpState extends State<PasswordOtp> {
  //controllers
  TextEditingController _codeController = TextEditingController();
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

  onPressConfirm() async {
    // var code = _codeController.text.toString();
    String  code = codeInputs.join();
    var password = _passwordController.text.toString();
    var password_confirm = _passwordConfirmController.text.toString();

    if (code == "") {
      ToastComponent.showDialog(AppLocalizations.of(context).password_otp_screen_code_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    } else if (password == "") {
      ToastComponent.showDialog(AppLocalizations.of(context).password_otp_screen_password_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    } else if (password_confirm == "") {
      ToastComponent.showDialog(AppLocalizations.of(context).password_otp_screen_password_confirm_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    } else if (password.length < 6) {
      ToastComponent.showDialog(
          AppLocalizations.of(context).password_otp_screen_password_length_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    } else if (password != password_confirm) {
      ToastComponent.showDialog(AppLocalizations.of(context).password_otp_screen_password_match_warning, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
      return;
    }

    var passwordConfirmResponse =
        await AuthRepository().getPasswordConfirmResponse(code, password);

    if (passwordConfirmResponse.result == false) {
      ToastComponent.showDialog(passwordConfirmResponse.message, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
    } else {
      ToastComponent.showDialog(passwordConfirmResponse.message, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);

      Navigator.pushReplacement(context, MaterialPageRoute(builder: (context) {
        return Login();
      }));
    }
  }

  onTapResend() async {
    var passwordResendCodeResponse = await AuthRepository()
        .getPasswordResendCodeResponse(widget.email_or_code, widget.verify_by);

    if (passwordResendCodeResponse.result == false) {
      ToastComponent.showDialog(passwordResendCodeResponse.message, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
    } else {
      ToastComponent.showDialog(passwordResendCodeResponse.message, context,
          gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
    }
  }


  var codeInputs = List<String>.filled(6, ""); // Creates fixed-length list.
 bool _isPasswordVisible = false;
  bool _isConfirmPasswordVisible = false;

  @override
  Widget build(BuildContext context) {
    String _verify_by = widget.verify_by; //phone or email
    final _screen_height = MediaQuery.of(context).size.height;
    final _screen_width = MediaQuery.of(context).size.width;
    // return Directionality(
    //   textDirection: app_language_rtl.$ ? TextDirection.rtl : TextDirection.ltr,
    //   child: Scaffold(
    //     backgroundColor: Colors.white,
    //     body: Stack(
    //       children: [
    //         Container(
    //           width: _screen_width * (3 / 4),
    //           child: Image.asset(
    //               "assets/splash_login_registration_background_image.png"),
    //         ),
    //         Container(
    //           width: double.infinity,
    //           child: SingleChildScrollView(
    //               child: Column(
    //             crossAxisAlignment: CrossAxisAlignment.center,
    //             children: [
    //               Padding(
    //                 padding: const EdgeInsets.only(top: 40.0, bottom: 15),
    //                 child: Container(
    //                   width: 75,
    //                   height: 75,
    //                   child:
    //                       Image.asset('assets/login_registration_form_logo.png'),
    //                 ),
    //               ),
    //               Padding(
    //                 padding: const EdgeInsets.only(bottom: 20.0),
    //                 child: Text(
    //                   AppLocalizations.of(context).password_otp_screen_enter_the_code_sent,
    //                   style: TextStyle(
    //                       color: MyTheme.accent_color,
    //                       fontSize: 18,
    //                       fontWeight: FontWeight.w600),
    //                 ),
    //               ),
    //               Padding(
    //                 padding: const EdgeInsets.only(bottom: 16.0),
    //                 child: Container(
    //                     width: _screen_width * (3 / 4),
    //                     child: _verify_by == "email"
    //                         ? Text(
    //                         AppLocalizations.of(context).password_otp_screen_enter_verification_code_to_email,
    //                             textAlign: TextAlign.center,
    //                             style: TextStyle(
    //                                 color: MyTheme.dark_grey, fontSize: 14))
    //                         : Text(
    //                         AppLocalizations.of(context).password_otp_screen_enter_verification_code_to_phone,
    //                             textAlign: TextAlign.center,
    //                             style: TextStyle(
    //                                 color: MyTheme.dark_grey, fontSize: 14))),
    //               ),
    //               Container(
    //                 width: _screen_width * (3 / 4),
    //                 child: Column(
    //                   crossAxisAlignment: CrossAxisAlignment.start,
    //                   children: [
    //                     Padding(
    //                       padding: const EdgeInsets.only(bottom: 8.0),
    //                       child: Column(
    //                         crossAxisAlignment: CrossAxisAlignment.end,
    //                         children: [
    //                           Container(
    //                             height: 36,
    //                             child: TextField(
    //                               controller: _codeController,
    //                               autofocus: false,
    //                               decoration:
    //                                   InputDecorations.buildInputDecoration_1(
    //                                       hint_text: "A X B 4 J H"),
    //                             ),
    //                           ),
    //                         ],
    //                       ),
    //                     ),
    //                     Padding(
    //                       padding: const EdgeInsets.only(bottom: 4.0),
    //                       child: Text(
    //                         AppLocalizations.of(context).password_otp_screen_password,
    //                         style: TextStyle(
    //                             color: MyTheme.accent_color,
    //                             fontWeight: FontWeight.w600),
    //                       ),
    //                     ),
    //                     Padding(
    //                       padding: const EdgeInsets.only(bottom: 8.0),
    //                       child: Column(
    //                         crossAxisAlignment: CrossAxisAlignment.end,
    //                         children: [
    //                           Container(
    //                             height: 36,
    //                             child: TextField(
    //                               controller: _passwordController,
    //                               autofocus: false,
    //                               obscureText: true,
    //                               enableSuggestions: false,
    //                               autocorrect: false,
    //                               decoration:
    //                                   InputDecorations.buildInputDecoration_1(
    //                                       hint_text: "• • • • • • • •"),
    //                             ),
    //                           ),
    //                           Text(
    //                             AppLocalizations.of(context).password_otp_screen_password_length_recommendation,
    //                             style: TextStyle(
    //                                 color: MyTheme.textfield_grey,
    //                                 fontStyle: FontStyle.italic),
    //                           )
    //                         ],
    //                       ),
    //                     ),
    //                     Padding(
    //                       padding: const EdgeInsets.only(bottom: 4.0),
    //                       child: Text(
    //                         AppLocalizations.of(context).password_otp_screen_retype_password,
    //                         style: TextStyle(
    //                             color: MyTheme.accent_color,
    //                             fontWeight: FontWeight.w600),
    //                       ),
    //                     ),
    //                     Padding(
    //                       padding: const EdgeInsets.only(bottom: 8.0),
    //                       child: Container(
    //                         height: 36,
    //                         child: TextField(
    //                           controller: _passwordConfirmController,
    //                           autofocus: false,
    //                           obscureText: true,
    //                           enableSuggestions: false,
    //                           autocorrect: false,
    //                           decoration: InputDecorations.buildInputDecoration_1(
    //                               hint_text: "• • • • • • • •"),
    //                         ),
    //                       ),
    //                     ),
    //                     Padding(
    //                       padding: const EdgeInsets.only(top: 40.0),
    //                       child: Container(
    //                         height: 45,
    //                         decoration: BoxDecoration(
    //                             border: Border.all(
    //                                 color: MyTheme.textfield_grey, width: 1),
    //                             borderRadius: const BorderRadius.all(
    //                                 Radius.circular(12.0))),
    //                         child: FlatButton(
    //                           minWidth: MediaQuery.of(context).size.width,
    //                           //height: 50,
    //                           color: MyTheme.accent_color,
    //                           shape: RoundedRectangleBorder(
    //                               borderRadius: const BorderRadius.all(
    //                                   Radius.circular(12.0))),
    //                           child: Text(
    //                             AppLocalizations.of(context).common_confirm_ucfirst,
    //                             style: TextStyle(
    //                                 color: Colors.white,
    //                                 fontSize: 14,
    //                                 fontWeight: FontWeight.w600),
    //                           ),
    //                           onPressed: () {
    //                             onPressConfirm();
    //                           },
    //                         ),
    //                       ),
    //                     ),
    //                   ],
    //                 ),
    //               ),
    //               Padding(
    //                 padding: const EdgeInsets.only(top: 100),
    //                 child: InkWell(
    //                   onTap: () {
    //                     onTapResend();
    //                   },
    //                   child: Text(AppLocalizations.of(context).password_otp_screen_resend_code,
    //                       textAlign: TextAlign.center,
    //                       style: TextStyle(
    //                           color: MyTheme.accent_color,
    //                           decoration: TextDecoration.underline,
    //                           fontSize: 13)),
    //                 ),
    //               ),
    //             ],
    //           )),
    //         )
    //       ],
    //     ),
    //   ),
    // );
    return Directionality(
      textDirection: app_language_rtl.$ ? TextDirection.rtl : TextDirection.ltr,
      child:Scaffold(
        backgroundColor: Colors.white,
        resizeToAvoidBottomInset: false,
        body: Column(
          children: [
            Expanded(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    children: [
                      addVerticalSpace(60.h),
                      Image.asset("assets/Enter-OTP-pana-1.png"),
                      addVerticalSpace(16.h),
                      Text(
                        "Confirm Email",
                        style: GoogleFonts.poppins(
                          fontSize: 28.0.sp,
                          fontWeight: FontWeight.w700,
                          letterSpacing: -0.24,
                        ),
                        textAlign: TextAlign.center,
                      ),
                      addVerticalSpace(8.h),
                      Text(
                        "We have sent you a code in your email,\nPlease type it below",
                        style: GoogleFonts.poppins(
                          fontSize: 15.0.sp,
                          fontWeight: FontWeight.w400,
                          letterSpacing: -0.24,
                        ),
                        textAlign: TextAlign.center,
                      ),
                    ],
                  ),
                  Form(
                    child: Column(
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                          children: [
                            SizedBox(
                              width: 50.0.w,
                              height: 57.0.h,
                              child: TextFormField(
                                style: Theme.of(context)
                                    .textTheme
                                    .headline6
                                    .copyWith(height: 1.0),
                                keyboardType: TextInputType.number,
                                decoration: InputDecoration(
                                    border: OutlineInputBorder(
                                      borderRadius:
                                      BorderRadius.all(Radius.circular(10.0)),
                                      borderSide: BorderSide(
                                        color: Colors.black,
                                        width: 1.5,
                                      ),
                                    )),
                                textAlign: TextAlign.center,
                                textAlignVertical: TextAlignVertical.center,
                                onChanged: (value) {
                                  if (value.length == 1) {
                                    codeInputs[0] = value;
                                    FocusScope.of(context).nextFocus();
                                  }
                                },
                                inputFormatters: [
                                  LengthLimitingTextInputFormatter(1),
                                  FilteringTextInputFormatter.digitsOnly,
                                ],
                              ),
                            ),
                            SizedBox(
                              width: 50.0.w,
                              height: 57.0.h,
                              child: TextFormField(
                                style: Theme.of(context)
                                    .textTheme
                                    .headline6
                                    .copyWith(height: 1.0),
                                keyboardType: TextInputType.number,
                                decoration: InputDecoration(
                                    border: OutlineInputBorder(
                                      borderRadius:
                                      BorderRadius.all(Radius.circular(10.0)),
                                      borderSide: BorderSide(
                                        color: Colors.black,
                                        width: 1.5,
                                      ),
                                    )),
                                textAlign: TextAlign.center,
                                textAlignVertical: TextAlignVertical.center,
                                onChanged: (value) {
                                  if (value.length == 1) {
                                    codeInputs[1] = value;

                                    FocusScope.of(context).nextFocus();
                                  }
                                },
                                inputFormatters: [
                                  LengthLimitingTextInputFormatter(1),
                                  FilteringTextInputFormatter.digitsOnly,
                                ],
                              ),
                            ),
                            SizedBox(
                              width: 50.0.w,
                              height: 57.0.h,
                              child: TextFormField(
                                style: Theme.of(context)
                                    .textTheme
                                    .headline6
                                    .copyWith(height: 1.0),
                                keyboardType: TextInputType.number,
                                decoration: InputDecoration(
                                    border: OutlineInputBorder(
                                      borderRadius:
                                      BorderRadius.all(Radius.circular(10.0)),
                                      borderSide: BorderSide(
                                        color: Colors.black,
                                        width: 1.5,
                                      ),
                                    )),
                                textAlign: TextAlign.center,
                                textAlignVertical: TextAlignVertical.center,
                                onChanged: (value) {
                                  if (value.length == 1) {
                                    codeInputs[2] = value;
                                    FocusScope.of(context).nextFocus();
                                  }
                                },
                                inputFormatters: [
                                  LengthLimitingTextInputFormatter(1),
                                  FilteringTextInputFormatter.digitsOnly,
                                ],
                              ),
                            ),
                            SizedBox(
                              width: 50.0.w,
                              height: 57.0.h,
                              child: TextFormField(
                                style: Theme.of(context)
                                    .textTheme
                                    .headline6
                                    .copyWith(height: 1.0),
                                keyboardType: TextInputType.number,
                                decoration: InputDecoration(
                                    border: OutlineInputBorder(
                                      borderRadius:
                                      BorderRadius.all(Radius.circular(10.0)),
                                      borderSide: BorderSide(
                                        color: Colors.black,
                                        width: 1.5,
                                      ),
                                    )),
                                textAlign: TextAlign.center,
                                textAlignVertical: TextAlignVertical.center,
                                onChanged: (value) {
                                  if (value.length == 1) {
                                    codeInputs[3] = value;
                                    FocusScope.of(context).nextFocus();
                                  }
                                },
                                inputFormatters: [
                                  LengthLimitingTextInputFormatter(1),
                                  FilteringTextInputFormatter.digitsOnly,
                                ],
                              ),
                            ),
                            SizedBox(
                              width: 50.0.w,
                              height: 57.0.h,
                              child: TextFormField(
                                style: Theme.of(context)
                                    .textTheme
                                    .headline6
                                    .copyWith(height: 1.0),
                                keyboardType: TextInputType.number,
                                decoration: InputDecoration(
                                    border: OutlineInputBorder(
                                      borderRadius:
                                      BorderRadius.all(Radius.circular(10.0)),
                                      borderSide: BorderSide(
                                        color: Colors.black,
                                        width: 1.5,
                                      ),
                                    )),
                                textAlign: TextAlign.center,
                                textAlignVertical: TextAlignVertical.center,
                                onChanged: (value) {
                                  if (value.length == 1) {
                                    codeInputs[4] = value;

                                    FocusScope.of(context).nextFocus();
                                  }
                                },
                                inputFormatters: [
                                  LengthLimitingTextInputFormatter(1),
                                  FilteringTextInputFormatter.digitsOnly,
                                ],
                              ),
                            ),
                            SizedBox(
                              width: 50.0.w,
                              height: 57.0.h,
                              child: TextFormField(
                                style: Theme.of(context)
                                    .textTheme
                                    .headline6
                                    .copyWith(height: 1.0),
                                keyboardType: TextInputType.number,
                                decoration: InputDecoration(
                                    border: OutlineInputBorder(
                                      borderRadius:
                                      BorderRadius.all(Radius.circular(10.0)),
                                      borderSide: BorderSide(
                                        color: Colors.black,
                                        width: 1.5,
                                      ),
                                    )),
                                textAlign: TextAlign.center,
                                textAlignVertical: TextAlignVertical.center,
                                onChanged: (value) {
                                  if (value.length == 1) {
                                    codeInputs[5] = value;

                                    FocusScope.of(context).nextFocus();
                                  }
                                },
                                inputFormatters: [
                                  LengthLimitingTextInputFormatter(1),
                                  FilteringTextInputFormatter.digitsOnly,
                                ],
                              ),
                            ),

                          ],
                        ),
                        addVerticalSpace(20.h),
                        Padding(
                          padding: EdgeInsets.symmetric(horizontal: defaultPadding* 1.5),
                          child: Column(
                            children: [
                              BAMOutlinedInput(hintText: "Set New Password", controller: _passwordController , obscureText: !_isConfirmPasswordVisible , maxLines: 1 ,trailing: IconButton(
                                onPressed: () {
                                  setState(() {
                                    _isConfirmPasswordVisible = !_isConfirmPasswordVisible;
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
                              ),),
                              addVerticalSpace(25.h),
                              BAMOutlinedInput(hintText: "Confirm Password", controller: _passwordConfirmController,obscureText: !_isPasswordVisible , maxLines: 1 ,trailing: IconButton(
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
                              )
                            ],
                          ),
                        ),

                      ],
                    ),
                  )
                ],
              ),
              flex: 3,
            ),
            Expanded(
              flex:1,
              child: Center(
                child: Column(
                  children: [
                    addVerticalSpace(25.h),
                    BAMElevatedButton(
                        onPress: () {
                         String  formateedCode = codeInputs.join();
                          print(formateedCode);
                         onPressConfirm();
                          // Navigator.of(context).push(MaterialPageRoute(
                          //     builder: (context) => NewPasswordScreen()));
                        },
                        text: "Confirm"),
                    addVerticalSpace(10.0.h),
                    BAMTextButton(text: "Resend Code", onPress: (){
                      onTapResend();
                    })

                  ],
                ),
              ),
            ),
          ],
        ),
      ) ,
    );
  }
}
