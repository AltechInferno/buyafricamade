
import 'package:active_ecommerce_flutter/constants/colors.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
class BAMElevatedButton extends StatelessWidget {
  BAMElevatedButton({Key key, @required this.onPress, @required this.text, this.size})
      : super(key: key);
  final VoidCallback onPress;
  final String text;
  final Size size;
  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
          gradient: primaryLinearGradient,
          borderRadius: BorderRadius.all(Radius.circular(94.0.r))),
      child: ElevatedButton(
        onPressed: onPress,
        child: Text(text,
            style: GoogleFonts.poppins(
                fontSize: 16.0.sp, height: 1.5, fontWeight: FontWeight.w500)),
        style: ButtonStyle(
            fixedSize:
                MaterialStateProperty.all<Size>(size ?? Size(200.0.w, 62.0.h)),
            shape: MaterialStateProperty.all<OutlinedBorder>(
                RoundedRectangleBorder(
                    borderRadius: BorderRadius.all(Radius.circular(94.0.r)))),
            elevation: MaterialStateProperty.all(0),
            shadowColor: MaterialStateProperty.all<Color>(Colors.transparent),
            backgroundColor:
                MaterialStateProperty.all<Color>(Colors.transparent)),
      ),
    );
  }
}
