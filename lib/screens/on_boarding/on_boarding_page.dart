import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
class OnBoardingPage extends StatelessWidget {
  const OnBoardingPage({Key key, @required this.img, @required this.text})
      : super(key: key);
  final String img;
  final String text;
  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(left: 0, right: 0, top: 100.h),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Container(
            child: Image.asset(
              "assets/$img.png",
              width: MediaQuery.of(context).size.width - 40.0,
              height: 305.0.h,
              fit: BoxFit.fitHeight,
            ),
          ),
          Text(
            text,
            textAlign: TextAlign.center,
            style: GoogleFonts.poppins(
                fontWeight: FontWeight.w700, height: 1.5, fontSize: 20.0.sp),
          )
        ],
      ),
    );
  }
}
