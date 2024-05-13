import 'package:active_ecommerce_flutter/constants/colors.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class BAMOutlinedButton extends StatelessWidget {
  const BAMOutlinedButton({Key key, @required this.onPress, @required this.text, this.size, this.textColor}) : super(key: key);
  final VoidCallback onPress;
  final String text;
  final Size size;
  final Color textColor;
  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
          border: Border.all(color: primaryColor, width: 1.0),
          borderRadius: BorderRadius.all(Radius.circular(94.0))),
      child: OutlinedButton(
        onPressed: onPress,
        child: Text(text,
            style: GoogleFonts.poppins(
              color: textColor,
                fontSize: 16.0, height: 1.5, fontWeight: FontWeight.w400)),
        style: ButtonStyle(
            fixedSize: MaterialStateProperty.all<Size>(size ?? Size(200.0, 62.0)),
            side: MaterialStateProperty.all<BorderSide>(BorderSide.none),
            shape: MaterialStateProperty.all<OutlinedBorder>(
                RoundedRectangleBorder(
                    side: BorderSide(width: 1.0, color: primaryColor),
                    borderRadius: BorderRadius.all(Radius.circular(94.0)))),
            elevation: MaterialStateProperty.all(0),
            shadowColor: MaterialStateProperty.all<Color>(Colors.transparent),
            backgroundColor:
                MaterialStateProperty.all<Color>(Colors.transparent)),
      ),
    );
  }
}
