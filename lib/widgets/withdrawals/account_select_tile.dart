
import 'package:active_ecommerce_flutter/constants/colors.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AccountSelectTile extends StatelessWidget {
  const AccountSelectTile({Key key, @required this.iconUrl, @required this.details}) : super(key: key);
  final String iconUrl;
  final String details;
  @override
  Widget build(BuildContext context) {
    return Ink(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(20.0),
        color: Colors.white,
        boxShadow: <BoxShadow>[
          BoxShadow(
            color: Color(0xFF000000).withOpacity(0.1),
            offset: Offset(0.0, 4.0),
            blurRadius: 20.0,
          ),
        ],
      ),
      child: ListTile(
        contentPadding:
            EdgeInsets.only(top: 8.0, bottom: 8.0, right: 16.0, left: 8.0),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20.0),
        ),
        minVerticalPadding: 0.0,
        onTap: () {
          // Navigator.push(context, MaterialPageRoute(builder: (context)=>WithdrawalConfirmScreen()));
        },
        title: Text(details,
            style: GoogleFonts.poppins(
                fontWeight: FontWeight.w400, fontSize: 14.0)),
        leading: Container(
          padding: EdgeInsets.symmetric(vertical: 7.0, horizontal: 10.0),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(20.0),
            color: Colors.white,
            boxShadow: <BoxShadow>[
              BoxShadow(
                color: Color(0xFF000000).withOpacity(0.05),
                offset: Offset(0.0, 1.0),
                blurRadius: 25.0,
              ),
            ],
          ),
          child: ClipRect(child: Image.asset("assets/images/earnings/$iconUrl.png")),
        ),
        trailing: Text("Select",
            style: GoogleFonts.poppins(
                color: primaryColor,
                fontSize: 14.0,
                fontWeight: FontWeight.w500)),
      ),
    );
  }
}
